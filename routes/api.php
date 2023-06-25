<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\UserController;



Route::controller(App\Http\Controllers\API\Auth\LoginController::class)->group(function () {
    Route::get('/logout', 'logout')->name('logout');
    Route::get('/social-login/redirect/{provider}', 'redirectToProvider')->name('social.login');
    Route::get('/social-login/{provider}/callback', 'handleProviderCallback')->name('social.callback');
});

Route::controller(App\Http\Controllers\API\Auth\VerificationController::class)->group(function () {
    Route::get('/email/resend', 'resend')->name('verification.resend');
    Route::get('/verification-confirmation/{code}', 'verification_confirmation')->name('email.verification.confirmation');
});

Route::get('/theme/{name?}', [\App\Http\Controllers\API\HomeController::class,'theme']);

Route::get('/home', [\App\Http\Controllers\API\HomeController::class, 'index']);
Route::get('/brands', [\App\Http\Controllers\API\HomeController::class, 'allBrands']);
Route::get('/categories', [\App\Http\Controllers\API\HomeController::class, 'allCategories']);

# products
Route::get('/products', [\App\Http\Controllers\API\ProductController::class, 'index']);
Route::get('/products/{slug}', [\App\Http\Controllers\API\ProductController::class, 'show']);
Route::post('/products/get-variation-info', [\App\Http\Controllers\API\ProductController::class, 'getVariationInfo']);
Route::post('/products/show-product-info', [\App\Http\Controllers\API\ProductController::class, 'showInfo']);

# carts
Route::get('/carts', [\App\Http\Controllers\API\CartsController::class, 'index']);
Route::post('/add-to-cart', [\App\Http\Controllers\API\CartsController::class, 'store']);
Route::post('/update-cart', [\App\Http\Controllers\API\CartsController::class, 'update']);
Route::post('/apply-coupon', [\App\Http\Controllers\API\CartsController::class, 'applyCoupon']);
Route::get('/clear-coupon', [\App\Http\Controllers\API\CartsController::class, 'clearCoupon']);

# blogs
Route::get('/blogs', [\App\Http\Controllers\API\HomeController::class, 'allBlogs'])->name('home.blogs');
Route::get('/blogs/{slug}', [\App\Http\Controllers\API\HomeController::class, 'showBlog'])->name('home.blogs.show');

# campaigns
Route::get('/campaigns', [App\Http\Controllers\API\HomeController::class, 'campaignIndex'])->name('home.campaigns');
Route::get('/campaigns/{slug}', [App\Http\Controllers\API\HomeController::class, 'showCampaign'])->name('home.campaigns.show');

# coupons
Route::get('/coupons', [App\Http\Controllers\API\HomeController::class, 'allCoupons'])->name('home.coupons');

# pages
Route::get('/pages/about-us', [App\Http\Controllers\API\HomeController::class, 'aboutUs'])->name('home.pages.aboutUs');
Route::get('/pages/contact-us', [App\Http\Controllers\API\HomeController::class, 'contactUs'])->name('home.pages.contactUs');
Route::get('/pages/{slug}', [App\Http\Controllers\API\HomeController::class, 'showPage'])->name('home.pages.show');

# contact us message
Route::post('/contact-us', [App\Http\Controllers\API\ContactUsController::class, 'store'])->name('contactUs.store');

# Subscribed Users
Route::post('/subscribers', [App\Http\Controllers\API\SubscribersController::class, 'store'])->name('subscribe.store');

# addresses
Route::post('/get-states', [App\Http\Controllers\API\AddressController::class, 'getStates'])->name('address.getStates');
Route::post('/get-cities', [App\Http\Controllers\API\AddressController::class, 'getCities'])->name('address.getCities');

# authenticated routes
Route::group(['prefix' => '', 'middleware' => ['customer', 'verified', 'isBanned']], function () {
    # customer routes
    Route::get('/customer-dashboard', [App\Http\Controllers\API\CustomerController::class, 'index'])->name('customers.dashboard');
    Route::get('/customer-order-history', [App\Http\Controllers\API\CustomerController::class, 'orderHistory'])->name('customers.orderHistory');
    Route::get('/customer-address', [App\Http\Controllers\API\CustomerController::class, 'address'])->name('customers.address');
    Route::get('/customer-profile', [App\Http\Controllers\API\CustomerController::class, 'profile'])->name('customers.profile');
    Route::post('/customer-profile', [App\Http\Controllers\API\CustomerController::class, 'updateProfile'])->name('customers.updateProfile');

    # wishlist
    Route::get('/wishlist', [App\Http\Controllers\API\WishlistController::class, 'index'])->name('customers.wishlist');
    Route::post('/add-to-wishlist', [App\Http\Controllers\API\WishlistController::class, 'store'])->name('customers.wishlist.store');
    Route::get('/delete-wishlist/{id}', [App\Http\Controllers\API\WishlistController::class, 'delete'])->name('customers.wishlist.delete');

    # checkout
    Route::get('/checkout', [App\Http\Controllers\API\CheckoutController::class, 'index'])->name('checkout.proceed');
    Route::post('/get-checkout-logistics', [App\Http\Controllers\API\CheckoutController::class, 'getLogistic'])->name('checkout.getLogistic');
    Route::post('/shipping-amount', [App\Http\Controllers\API\CheckoutController::class, 'getShippingAmount'])->name('checkout.getShippingAmount');
    Route::post('/checkout-complete', [App\Http\Controllers\API\CheckoutController::class, 'complete'])->name('checkout.complete');
    Route::get('/orders/{code}/invoice', [App\Http\Controllers\API\CheckoutController::class, 'success'])->name('checkout.success');

    # address
    Route::post('/new-address', [App\Http\Controllers\API\AddressController::class, 'store'])->name('address.store');
    Route::post('/edit-address', [App\Http\Controllers\API\AddressController::class, 'edit'])->name('address.edit');
    Route::post('/update-address', [App\Http\Controllers\API\AddressController::class, 'update'])->name('address.update');
    Route::get('/delete-address/{id}', [App\Http\Controllers\API\AddressController::class, 'delete'])->name('address.delete');

    # order tracking
    Route::get('/track-order', [App\Http\Controllers\API\OrderTrackingController::class, 'index'])->name('customers.trackOrder');
});

# media files routes
Route::group(['prefix' => '', 'middleware' => ['auth']], function () {
    Route::get('/media-manager/get-files', [App\Http\Controllers\API\MediaManagerController::class, 'index'])->name('uppy.index');
    Route::get('/media-manager/get-selected-files', [App\Http\Controllers\API\MediaManagerController::class, 'selectedFiles'])->name('uppy.selectedFiles');
    Route::post('/media-manager/add-files', [App\Http\Controllers\API\MediaManagerController::class, 'store'])->name('uppy.store');
    Route::get('/media-manager/delete-files/{id}', [App\Http\Controllers\API\MediaManagerController::class, 'delete'])->name('uppy.delete');
});

# payment gateways
Route::group(['prefix' => ''], function () {
    # paypal
    Route::get('/paypal/success', [App\Http\Controllers\API\Backend\Payments\Paypal\PaypalController::class, 'success'])->name('paypal.success');
    Route::get('/paypal/cancel', [App\Http\Controllers\API\Backend\Payments\Paypal\PaypalController::class, 'cancel'])->name('paypal.cancel');

    # stripe
    Route::any('/stripe/create-session', [App\Http\Controllers\API\Backend\Payments\Stripe\StripePaymentController::class, 'checkoutSession'])->name('stripe.checkoutSession');
    Route::get('/stripe/success', [App\Http\Controllers\API\Backend\Payments\Stripe\StripePaymentController::class, 'success'])->name('stripe.success');
    Route::get('/stripe/cancel', [App\Http\Controllers\API\Backend\Payments\Stripe\StripePaymentController::class, 'cancel'])->name('stripe.cancel');

    # paytm
    Route::any('/paytm/callback', [App\Http\Controllers\API\Backend\Payments\Paytm\PaytmPaymentController::class, 'callback'])->name('paytm.callback');

    # razorpay
    Route::post('razorpay/payment', [App\Http\Controllers\API\Backend\Payments\Razorpay\RazorpayController::class, 'payment'])->name('razorpay.payment');
});