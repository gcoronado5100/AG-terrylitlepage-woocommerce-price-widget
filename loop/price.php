<?php

/**
 * Loop Price
 *
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     1.6.4
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

global $product;
$siteURL = site_url();


$curl = curl_init();

curl_setopt_array($curl, [
    CURLOPT_URL => "$siteURL/wp-json/wholesale/v1/products/$product->id",
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_ENCODING => "",
    CURLOPT_MAXREDIRS => 10,
    CURLOPT_TIMEOUT => 30,
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_CUSTOMREQUEST => "GET",
    CURLOPT_HTTPHEADER => [
        "Accept: */*",
        "Authorization: Basic Y2tfYmJkZDkzOTQ5YzU2OGExYmYxZmVmMjgzYjc1MDI4YTcxNTBjZGYyYzpjc19kMmRmMDZlMGQ2NjAwZjdlNjY5ZDU2MGVkNjk5YzJkMjk0ZTU5M2Rj",
        "User-Agent: Thunder Client (https://www.thunderclient.com)"
    ],
]);

$response = curl_exec($curl);
$err = curl_error($curl);

curl_close($curl);

if ($err) {
    echo "cURL Error #:" . $err;
} else {
    $productDetails = json_decode($response);
}



?>

<?php if ($price_html = $product->get_price_html()) : ?>
    <?php $user = wp_get_current_user();

    if (!in_array('wholesale_customer', (array) $user->roles)) : ?>


        <div class="price__container">
            <span><strong>Member's Price</strong></span>



            <?php if ($productDetails->wholesale_data->wholesale_price->wholesale_customer != null) : ?>
                <span class="price"><?= "$" . $productDetails->wholesale_data->wholesale_price->wholesale_customer ?></span>
            <?php else :
                $member_role_price_rule =  round(floatval(get_option(WWPP_OPTION_WHOLESALE_ROLE_GENERAL_DISCOUNT_MAPPING, array([]))["wholesale_customer"]), 2, PHP_ROUND_HALF_UP);
            ?>
                <span class="price"><?= '$ ' . round(($product->price - ($product->price * ($member_role_price_rule / 100))), 2, PHP_ROUND_HALF_UP); ?></span>
            <?php endif; ?>
            <span><strong>Regular Price</strong></span>
            <span class="price"><?= wp_kses_post($price_html); ?></span>
        </div>

    <?php else : ?>

        <span class="price"><?php echo wp_kses_post($price_html); ?></span>
    <?php endif; ?>


<?php endif; ?>