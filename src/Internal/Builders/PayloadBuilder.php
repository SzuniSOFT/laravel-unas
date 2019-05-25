<?php


namespace SzuniSoft\Unas\Internal\Builders;


use Illuminate\Support\Collection;
use SzuniSoft\Unas\Internal\ApiSchema;
use SzuniSoft\Unas\Internal\Optimizers\OrderOptimizer;
use XMLWriter;
use function is_array;

class PayloadBuilder
{

    use OrderOptimizer;

    /**
     * @param callable $cb
     *
     * @return string
     */
    protected static function builder(callable $cb)
    {
        $writer = new XMLWriter();
        $writer->openMemory();
        $writer->startDocument('1.0', 'UTF-8');
        $cb($writer);
        $writer->endElement();
        return $writer->outputMemory();
    }

    /**
     * @param array          $arr
     *
     * @param XMLWriter|null $writer
     *
     * @return string
     */
    protected static function buildByAssocRecursively(array $arr, XMLWriter &$writer = null)
    {
        // Create builder and start recursion.
        if (!$writer) {
            return self::builder(function (XMLWriter $writer) use (&$arr) {
                return self::buildByAssocRecursively($arr, $writer);
            });
        }

        // Recursive entry point.
        foreach ($arr as $key => $value) {

            $writer->startElement($key);
            {
                // Render scalars.
                if (!is_array($value)) {
                    $writer->writeCdata($value);
                }
                // Call recursively.
                else {
                    self::buildByAssocRecursively($value, $writer);
                }
            }
            $writer->endElement();
        }
    }

    /**
     * @param $token
     *
     * @return string
     */
    public static function forPremiumAuthorization($token)
    {
        return self::builder(function (XMLWriter $writer) use (&$token) {
            $writer->startElement('Params');
            {
                $writer->writeElement('ApiKey', $token);
            }
            $writer->endElement();
        });
    }

    /**
     * @param $username
     * @param $password
     * @param $shopId
     * @param $authCode
     *
     * @return string
     */
    public static function forLegacyAuthorization($username, $password, $shopId, $authCode)
    {
        return self::builder(function (XMLWriter $writer) use (&$username, &$password, &$shopId, &$authCode) {
            $writer->startElement('Auth');
            {
                $writer->writeAttribute('Username', $username);
                $writer->writeAttribute('PasswordCrypt', $password);
                $writer->writeAttribute('ShopId', $shopId);
                $writer->writeAttribute('AuthCode', $authCode);
            }
            $writer->endElement();
        });
    }

    /**
     * @param array $params
     *
     * @return string
     */
    protected static function writeGeneralParameters(array $params)
    {
        return self::builder(function (XMLWriter $writer) use (&$params) {

            if (!empty($params)) {
                $writer->startElement('Params');
                {
                    foreach ($params as $key => $value) {
                        $writer->writeElement(ApiSchema::keyify($key), $value);
                    }
                }
                $writer->endElement();
            }
        });
    }

    /**
     * @param array $params
     *
     * @return string
     */
    public static function forGetOrder(array $params)
    {
        return self::writeGeneralParameters($params);
    }

    /**
     * @param array $params
     *
     * @return string
     */
    public static function forGetProduct(array $params)
    {
        return self::writeGeneralParameters($params);
    }

    /**
     * @param Collection $orders
     *
     * @return string
     */
    public static function forSetOrder(Collection $orders)
    {
        /*return self::buildByAssocRecursively(
            $orders
                ->map(function (Order $order) {
                    return self::optimizeOrderForSave($order);
                })
                ->toArray()
        );*/
    }

}
