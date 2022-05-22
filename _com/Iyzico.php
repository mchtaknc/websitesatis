<?php
use Iyzipay\Model\Address;
use Iyzipay\Model\BasketItem;
use Iyzipay\Model\BasketItemType;
use Iyzipay\Model\Buyer;
use Iyzipay\Model\CheckoutFormInitialize;
use Iyzipay\Model\Currency;
use Iyzipay\Model\Locale;
use Iyzipay\Model\PaymentGroup;
use Iyzipay\Options;
use Iyzipay\Request\CreateCheckoutFormInitializeRequest;
use Iyzipay\Model\CheckoutForm;
use Iyzipay\Request\RetrieveCheckoutFormRequest;

class Iyzico
{
    protected $options;
    protected $request;
    protected $basketItems;

    public function __construct()
    {
        $this->options = new Options();
        $this->options->setApiKey('sandbox-qHhLXpcQGVC4A7XWWfSAbijyqHlk0wib');
        $this->options->setSecretKey('sandbox-Tth1ia5tuk63io474LM822A9feC7TJYL');
        $this->options->setBaseUrl('https://sandbox-api.iyzipay.com');
        $this->basketItems = [];
    }

    public function setForm(array $params)
    {
        $this->request = new CreateCheckoutFormInitializeRequest();
        $this->request->setLocale(Locale::TR);
        $this->request->setConversationId($params['conversationId']);
        $this->request->setPrice($params['price']);
        $this->request->setPaidPrice($params['paidPrice']);
        $this->request->setCurrency(Currency::TL);
        $this->request->setBasketId($params['basketId']);
        $this->request->setPaymentGroup(PaymentGroup::PRODUCT);
        $this->request->setCallbackUrl($params['domain'].'odeme-sonuc');
        $this->request->setEnabledInstallments(array(2, 3, 6, 9));

        return $this;
    }

    public function setBuyer(array $params)
    {
        $buyer = new Buyer();
        $buyer->setId($params['id']);
        $buyer->setName($params['firstname']);
        $buyer->setSurname($params['lastname']);
        $buyer->setGsmNumber($params['phone']);
        $buyer->setEmail($params['email']);
        $buyer->setIdentityNumber($params['identityNumber']);
        $buyer->setRegistrationAddress($params['address']);
        $buyer->setIp($params['ip']);
        $buyer->setCity($params['city']);
        $buyer->setCountry($params['country']);
        $this->request->setBuyer($buyer);

        return $this;
    }

    public function setShipping(array $params)
    {
        $shippingAddress = new Address();
        $shippingAddress->setContactName($params['contactName']);
        $shippingAddress->setCity($params['city']);
        $shippingAddress->setCountry($params['country']);
        $shippingAddress->setAddress($params['address']);
        $this->request->setShippingAddress($shippingAddress);

        return $this;
    }

    public function setBilling(array $params)
    {
        $billingAddress = new Address();
        $billingAddress->setContactName($params['contactName']);
        $billingAddress->setCity($params['city']);
        $billingAddress->setCountry($params['country']);
        $billingAddress->setAddress($params['address']);
        $this->request->setBillingAddress($billingAddress);

        return $this;
    }

    public function setItems(array $items)
    {
        foreach ($items as $key => $value) {
            $basketItem = new BasketItem();
            $basketItem->setId($value['id']);
            $basketItem->setName($value['name']);
            $basketItem->setCategory1($value['category']);
            $basketItem->setItemType(BasketItemType::VIRTUAL);
            $basketItem->setPrice($value['price']);
            array_push($this->basketItems, $basketItem);
        }
        $this->request->setBasketItems($this->basketItems);

        return $this;
    }

    public function paymentForm()
    {
        $form = CheckoutFormInitialize::create($this->request, $this->options);
        return $form;
    }

    public function callbackForm($token, $conversationId)
    {
        $this->request = new RetrieveCheckoutFormRequest();
        $this->request->setLocale(\Iyzipay\Model\Locale::TR);
        $this->request->setConversationId($conversationId);
        $this->request->setToken($token);

        $checkoutForm = CheckoutForm::retrieve($this->request, $this->options);

        return $checkoutForm;
    }
}
