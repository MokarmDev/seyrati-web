<?php

namespace Botble\Ecommerce\Http\Controllers\Customers;

use Botble\ACL\Traits\RegistersUsers;
use Botble\Base\Facades\BaseHelper;
use Botble\Base\Http\Controllers\BaseController;
use Botble\Ecommerce\Events\CustomerEmailVerified;
use Botble\Ecommerce\Facades\EcommerceHelper;
use Botble\Ecommerce\Forms\Fronts\Auth\RegisterForm;
use Botble\Ecommerce\Http\Requests\RegisterRequest;
use Botble\Ecommerce\Models\Customer;
use Botble\Ecommerce\Models\CustomerOtp;
use Botble\Ecommerce\Services\WhatsAppService;
use Botble\JsValidation\Facades\JsValidator;
use Botble\SeoHelper\Facades\SeoHelper;
use Botble\Theme\Facades\Theme;
use Carbon\Carbon;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\URL;

class RegisterController extends BaseController
{
    use RegistersUsers;

    protected string $redirectTo = '/';
    protected WhatsAppService $whatsappService;

    public function __construct(WhatsAppService $whatsappService)
    {
        $this->middleware('customer.guest');
        $this->whatsappService = $whatsappService;
    }

    public function showRegistrationForm()
    {
        if (! EcommerceHelper::isCustomerRegistrationEnabled()) {
            abort(404);
        }

        SeoHelper::setTitle(__('Register'));

        Theme::breadcrumb()->add(__('Register'), route('customer.register'));

        if (! session()->has('url.intended') &&
            ! in_array(url()->previous(), [route('customer.login'), route('customer.register')])
        ) {
            session(['url.intended' => url()->previous()]);
        }

        Theme::asset()
            ->container('footer')
            ->usePath(false)
            ->add('js-validation', 'vendor/core/core/js-validation/js/js-validation.js', ['jquery'], version: '1.0.1');

        add_filter(THEME_FRONT_FOOTER, function ($html) {
            return $html . JsValidator::formRequest(RegisterRequest::class)->render();
        });

        return Theme::scope(
            'ecommerce.customers.register',
            ['form' => RegisterForm::create()],
            'plugins/ecommerce::themes.customers.register'
        )->render();
    }

    public function register(RegisterRequest $request)
    {
        if (! EcommerceHelper::isCustomerRegistrationEnabled()) {
            abort(404);
        }

        do_action('customer_register_validation', $request);

        // التحقق من تكوين WhatsApp إذا كان رقم الهاتف مطلوب
        $phoneRequired = get_ecommerce_setting('make_customer_phone_number_required', false) || 
                         in_array(EcommerceHelper::getLoginOption(), ['phone', 'email_or_phone']);
        
        if ($phoneRequired && $request->input('phone') && !$this->whatsappService->isConfigured()) {
            return $this
                ->httpResponse()
                ->setError()
                ->setNextUrl(route('customer.register'))
                ->setMessage(__('خدمة WhatsApp غير مفعلة. يرجى التواصل مع الإدارة'));
        }

        // إذا كان هناك رقم هاتف، استخدم التحقق عبر OTP (بدون إنشاء حساب حالياً)
        if ($request->input('phone') && $phoneRequired) {
         
            // حفظ بيانات التسجيل في الجلسة (بدون إنشاء الحساب)
            session([
                'pending_registration_data' => [
                    'name' => BaseHelper::clean($request->input('name')),
                    'email' => BaseHelper::clean($request->input('email') ?? null),
                    'phone' => BaseHelper::clean($request->input('phone')),
                    'password' => $request->input('password'), // سيتم تشفيره عند الإنشاء
                ],
                'otp_phone' => BaseHelper::clean($request->input('phone')),
                'otp_created_at' => now()->timestamp,
            ]);
            
            // إنشاء مستخدم مؤقت فقط لإنشاء OTP
            $tempCustomer = new Customer([
                'name' => BaseHelper::clean($request->input('name')),
                'email' => BaseHelper::clean($request->input('email') ?? null),
                'phone' => BaseHelper::clean($request->input('phone')),
            ]);
            
            // إنشاء OTP وإرساله
            $otp = CustomerOtp::generateWithoutCustomer($tempCustomer->phone, 'registration');
            
            // إرسال OTP عبر WhatsApp
            $sent = $this->whatsappService->sendOtp(
                $tempCustomer->phone,
                $otp->otp,
                $tempCustomer->name
            );

            if (!$sent) {
                // حذف OTP الفاشل
                $otp->delete();
                session()->forget(['pending_registration_data', 'otp_phone', 'otp_created_at']);

                return $this
                    ->httpResponse()
                    ->setError()
                    ->setNextUrl(route('customer.register'))
                    ->setMessage(__('فشل إرسال رمز التحقق. يرجى المحاولة مرة أخرى'));
            }


            
            return $this
                ->httpResponse()
                ->setNextUrl(route('customer.otp.verify'))
                ->setMessage(__('تم إرسال رمز التحقق إلى WhatsApp الخاص بك'));
        }

        // إذا كان التسجيل بدون OTP (البريد الإلكتروني فقط)
        /**
         * @var Customer $customer
         */
        $customer = $this->create($request->input());

        event(new Registered($customer));

        \Illuminate\Support\Facades\Log::info('Skipping OTP, Using Standard Registration', [
            'has_phone' => !empty($customer->phone),
            'phone_required' => $phoneRequired,
            'reason' => empty($customer->phone) ? 'No phone provided' : 'Phone not required',
        ]);

        // التحقق عبر البريد الإلكتروني (الطريقة القديمة)
        if (EcommerceHelper::isEnableEmailVerification()) {
            $this->registered($request, $customer);

            $message = __('We have sent you an email to verify your email. Please check and confirm your email address!');

            return $this
                ->httpResponse()
                ->setNextUrl(route('customer.login'))
                ->with(['auth_warning_message' => $message])
                ->setMessage($message);
        }

        // تفعيل مباشر بدون تحقق
        $customer->confirmed_at = Carbon::now();
        $customer->save();

        $this->guard()->login($customer);

        return $this
            ->httpResponse()
            ->setNextUrl($this->redirectPath())
            ->setMessage(__('Registered successfully!'));
    }

    protected function create(array $data)
    {
        return Customer::query()->create([
            'name' => BaseHelper::clean($data['name']),
            'email' => BaseHelper::clean($data['email'] ?? null),
            'phone' => BaseHelper::clean($data['phone'] ?? null),
            'password' => Hash::make($data['password']),
        ]);
    }

    protected function guard()
    {
        return auth('customer');
    }

    public function confirm(int|string $id, Request $request)
    {
        abort_unless(URL::hasValidSignature($request), 404);

        /**
         * @var Customer $customer
         */
        $customer = Customer::query()->findOrFail($id);

        $customer->confirmed_at = Carbon::now();
        $customer->save();

        $this->guard()->login($customer);

        CustomerEmailVerified::dispatch($customer);

        return $this
            ->httpResponse()
            ->setNextUrl(route('customer.overview'))
            ->setMessage(__('You successfully confirmed your email address.'));
    }

    public function resendConfirmation(Request $request)
    {
        /**
         * @var Customer $customer
         */
        $customer = Customer::query()->where('email', $request->input('email'))->first();

        if (! $customer) {
            return $this
                ->httpResponse()
                ->setError()
                ->setMessage(__('Cannot find this customer!'));
        }

        $customer->sendEmailVerificationNotification();

        return $this
            ->httpResponse()
            ->setMessage(__('We sent you another confirmation email. You should receive it shortly.'));
    }
}
