<?php
/**
 * Created by PhpStorm.
 * User: yangjingquan
 * Date: 2019-03-22
 * Time: 16:28
 */

return [
    //应用ID,您的APPID。
    'app_id' => "2016010801074707",

    //商户私钥
    'merchant_private_key' => "MIIEpQIBAAKCAQEA3GIUgC4DMfEHzEIIo//7P1XfHQmLFViW+zm7SiZNhw9p0+Q/mPi6u+cGUxwnnKvxRCOwclBBQK7kIwn1tzy1LB2KetCblaJ0K9MqZaVs5bCK3PO6PJ72T1YBOamJW6IUrce3FD6tttCCBOi7h7IQL2mM+S1NtdAZ2xOVFdApEKLvt+H7M3Hq4rc43fP2RzbpAQXEJmeEdjQNoO5PAc6TaeysPUQLc/oQiNh/jP3VWZfchpqK53/1pHobEh19+Vrhnq8KdH8cqZyCgCAy515+RTwY3gk3JhOrE+wVYTk4q56gQRx+UpCiszbdBU/ftCJpk+dKsqhBRkNYOfsdogIBhwIDAQABAoIBAQDBiPx56G5z4Di53s+ZvkYVQ8MQy/2xPEAy1WstXd+9mQKVyx8yplPOib6bI+GBi9nvvevJJ8N2G2BLiQaY6R4tZK/k1OedC7I7flEAPLsaJuR7zyog7HRgDaY7zi/LIdZJGlcj1ztUjV1xFDUDoJLoIChl9qwE24CpTFQjeM9nRB5Cb2QpP885RWSpjk+AjY9amHvuT07TaEF8rO/6mst/dCRpMuaWU1wvJaQznCAUQpAiSCJy3suIQwYjhFKIKnB5cvAxirfs6TG5qhit3mvOvqyYA7MKuw90cWFCVa57pAmWslfv/ikIoMTGt4S/MBIDHq+4E5CzlQLKs6LCPj2JAoGBAPHPAy9j8YxdxyNgzjMgiPSvOu9rV2P5pp2HkOCtupH/qOYPnesBz4aysWi7fe4uJOvmAfxtDS08HM7Mz26c/p+J2vlL+ovh6QrGhyEaJycBGZXhCWoLg7eTZ9fLI2CaOEhFXF8CD99b9SmCsSDoFxDm4IhkHLTaxVdyePhY0/JLAoGBAOlRKnSJV5QJxJYmTa3XSj4IRDCcbZSQkqozO6rMLn0ozvkG9UyIxYHONY6ij+Us3phNeBiAhNcZct1njreMl0pFUKQxEUKv2HI1wyNM7cezCntOzpCxSzJMNTw2jMOa+xhb/UNCAr6axlvYblf/uQ07M5eEdFpQ7fkF7kWWm4g1AoGAJcv573pr00kFS55iHNPFFJofWDUrH9FyHcWG+9esBg40VGG0iXtq+N8NpBVKheRomQcG4HIbUSrIRfr1oZCgrEdcFwOtUhgp2SlXDfGMlSgy256lXhIWsKc14CE8kmkIyyMsYR1tZbh2BdsL3NzqXhX4mtkrM4nOtvCJwCgCWSkCgYEA0lyVC8d9CAOalMpqRTAUR0PKv416m3WSVxSkWoWH8N7nZc4pQt0aPsP+s+DMHpRLCkTm4CiYHRxb3VXubn27NoKyh6eZ6WeEnszsaRqGVQSPkcfnJLxFkU72vJhuaDqG5FqkSYztzo7cA5lrO7pcTZqwFI+fHINVXK5FO8bj6nUCgYEAxP2f+N8O+f40it1JICPHP7YwDh7pCWAjs3j9DHo61exPR84baUHDY/0umwtdeLIcpXK6e1oEtIilticSKlc01YZ9iaJF66ow9H4VJLVK42uY2vhe+ZkYG40Uv8YqPUGare+4LcAUMx88idGVi/5BCrzhf9NZCUe0OuYa8p9ytsY=",

    //异步通知地址
    'notify_url' => "http://cpcyjulian.dxshuju.com/bis/alipayNotify/ailpay_notify",

    //同步跳转
    'return_url' => "http://cpcyjulian.dxshuju.com/bis",

    //编码格式
    'charset' => "UTF-8",

    //签名方式
    'sign_type' => "RSA2",

    //支付宝网关
    'gatewayUrl' => "https://openapi.alipay.com/gateway.do",

    //支付宝公钥,查看地址：https://openhome.alipay.com/platform/keyManage.htm 对应APPID下的支付宝公钥。
    'alipay_public_key' => "MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAr5uy3mHImb80QfvZy8bY18whqrZcqjv1rP8OS48/IA7iW99oB6UvsrFasnLoGlK5oJsiq5sfCZjyM7cMt4c86owr5p0VN0iVSSw5EHf89dMfTukyOE++Cez0fXudet8m1+F8ktEIM9b8gFDoheB1R+8m7AmER9w1xkEbkaBLPkLgD3prDXilR+S99SoTsRl1k/QkDX+ac32ZH78mU3xT2jY1FMVGhCptfcV2vMrf6uOBYvYDdIhwH+LUrqU0//WiD2saDjVrkNqum0JVRFkGe+HgozFk9qNDmBEruBO8/lGHJHG09kLIm/w9Atwu74BbvuJmy1sb+LhHSCemfURI/wIDAQAB",

];