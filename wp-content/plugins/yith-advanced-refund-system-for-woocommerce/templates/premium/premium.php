<style>
    .section{
        margin-left: -20px;
        margin-right: -20px;
        font-family: "Raleway",san-serif;
    }
    .section h1{
        text-align: center;
        text-transform: uppercase;
        color: #808a97;
        font-size: 35px;
        font-weight: 700;
        line-height: normal;
        display: inline-block;
        width: 100%;
        margin: 50px 0 0;
    }
    .section ul{
        list-style-type: disc;
        padding-left: 15px;
    }
    .section:nth-child(even){
        background-color: #fff;
    }
    .section:nth-child(odd){
        background-color: #f1f1f1;
    }
    .section .section-title img{
        display: table-cell;
        vertical-align: middle;
        width: auto;
        margin-right: 15px;
    }
    .section h2,
    .section h3 {
        display: inline-block;
        vertical-align: middle;
        padding: 0;
        font-size: 24px;
        font-weight: 700;
        color: #808a97;
        text-transform: uppercase;
    }

    .section .section-title h2{
        display: table-cell;
        vertical-align: middle;
        line-height: 25px;
    }

    .section-title{
        display: table;
    }

    .section h3 {
        font-size: 14px;
        line-height: 28px;
        margin-bottom: 0;
        display: block;
    }

    .section p{
        font-size: 13px;
        margin: 15px 0;
    }
    .section ul li{
        margin-bottom: 4px;
    }
    .landing-container{
        max-width: 750px;
        margin-left: auto;
        margin-right: auto;
        padding: 50px 0 30px;
    }
    .landing-container:after{
        display: block;
        clear: both;
        content: '';
    }
    .landing-container .col-1,
    .landing-container .col-2{
        float: left;
        box-sizing: border-box;
        padding: 0 15px;
    }
    .landing-container .col-1 img{
        width: 100%;
    }
    .landing-container .col-1{
        width: 55%;
    }
    .landing-container .col-2{
        width: 45%;
    }
    .premium-cta{
        background-color: #808a97;
        color: #fff;
        border-radius: 6px;
        padding: 20px 15px;
    }
    .premium-cta:after{
        content: '';
        display: block;
        clear: both;
    }
    .premium-cta p{
        margin: 7px 0;
        font-size: 14px;
        font-weight: 500;
        display: inline-block;
        width: 60%;
    }
    .premium-cta a.button{
        border-radius: 6px;
        height: 60px;
        float: right;
        background: url(<?php echo YITH_WCARS_ASSETS_URL ?>images/upgrade.png) #ff643f no-repeat 13px 13px;
        border-color: #ff643f;
        box-shadow: none;
        outline: none;
        color: #fff;
        position: relative;
        padding: 9px 50px 9px 70px;
    }
    .premium-cta a.button:hover,
    .premium-cta a.button:active,
    .premium-cta a.button:focus{
        color: #fff;
        background: url(<?php echo YITH_WCARS_ASSETS_URL ?>images/upgrade.png) #971d00 no-repeat 13px 13px;
        border-color: #971d00;
        box-shadow: none;
        outline: none;
    }
    .premium-cta a.button:focus{
        top: 1px;
    }
    .premium-cta a.button span{
        line-height: 13px;
    }
    .premium-cta a.button .highlight{
        display: block;
        font-size: 20px;
        font-weight: 700;
        line-height: 20px;
    }
    .premium-cta .highlight{
        text-transform: uppercase;
        background: none;
        font-weight: 800;
        color: #fff;
    }

    .section.one{
        background: url(<?php echo YITH_WCARS_ASSETS_URL ?>/images/01-bg.png) no-repeat #fff; background-position: 85% 75%
    }
    .section.two{
        background: url(<?php echo YITH_WCARS_ASSETS_URL ?>/images/02-bg.png) no-repeat #fff; background-position: 15% 100%;
    }
    .section.three{
        background: url(<?php echo YITH_WCARS_ASSETS_URL ?>/images/03-bg.png) no-repeat #fff; background-position: 85% 75%
    }
    .section.four{
        background: url(<?php echo YITH_WCARS_ASSETS_URL ?>/images/04-bg.png) no-repeat #fff; background-position: 15% 100%;
    }
    .section.five{
        background: url(<?php echo YITH_WCARS_ASSETS_URL ?>/images/05-bg.png) no-repeat #fff; background-position: 85% 75%
    }
    .section.six{
        background: url(<?php echo YITH_WCARS_ASSETS_URL ?>/images/06-bg.png) no-repeat #fff; background-position: 15% 100%;
    }
    .section.seven{
        background: url(<?php echo YITH_WCARS_ASSETS_URL ?>/images/07-bg.png) no-repeat #fff; background-position: 85% 75%
    }
    .section.eight{
        background: url(<?php echo YITH_WCARS_ASSETS_URL ?>/images/08-bg.png) no-repeat #fff; background-position: 15% 100%;
    }


    @media (max-width: 768px) {
        .section{margin: 0}
        .premium-cta p{
            width: 100%;
        }
        .premium-cta{
            text-align: center;
        }
        .premium-cta a.button{
            float: none;
        }
    }

    @media (max-width: 480px){
        .wrap{
            margin-right: 0;
        }
        .section{
            margin: 0;
        }
        .landing-container .col-1,
        .landing-container .col-2{
            width: 100%;
            padding: 0 15px;
        }
        .section-odd .col-1 {
            float: left;
            margin-right: -100%;
        }
        .section-odd .col-2 {
            float: right;
            margin-top: 65%;
        }
    }

    @media (max-width: 320px){
        .premium-cta a.button{
            padding: 9px 20px 9px 70px;
        }

        .section .section-title img{
            display: none;
        }
    }
</style>
<?php $admin = YITH_Advanced_Refund_System_Admin::get_instance(); ?>
<div class="landing">
    <div class="section section-cta section-odd">
        <div class="landing-container">
            <div class="premium-cta">
                <p>
                    <?php echo sprintf( __('Upgrade to %1$spremium version%2$s of %1$sYITH Advanced Refund System for WooCommerce%2$s to benefit from all features!','yith-advanced-refund-system-for-woocommerce'),'<span class="highlight">','</span>' );?>
                </p>
                <a href="<?php echo $admin->get_premium_landing_uri(); ?>" target="_blank" class="premium-cta-button button btn">
                    <span class="highlight"><?php _e('UPGRADE','yith-advanced-refund-system-for-woocommerce');?></span>
                    <span><?php _e('to the premium version','yith-advanced-refund-system-for-woocommerce');?></span>
                </a>
            </div>
        </div>
    </div>
    <div class="one section section-even clear">
        <h1><?php _e('Premium Features','yith-advanced-refund-system-for-woocommerce');?></h1>
        <div class="landing-container">
            <div class="col-1">
                <img src="<?php echo YITH_WCARS_ASSETS_URL ?>/images/01.png" />
            </div>
            <div class="col-2">
                <div class="section-title">
                    <img src="<?php echo YITH_WCARS_ASSETS_URL ?>/images/01-icon.png"/>
                    <h2><?php _e('Refund the entire order or a single product in it','yith-advanced-refund-system-for-woocommerce');?></h2>
                </div>
                <p>
                    <?php echo sprintf(__('Users who need to ask for a refund have a double choice, if you are using the premium version. They will also be able to %1$sclaim a refund for only one of the products in the order.%2$s', 'yith-advanced-refund-system-for-woocommerce'), '<b>', '</b>');?>
                </p>
                <p>
                    <?php _e('This will reduce refund requests for whole orders in case the customer is not satisfied with one product only.', 'yith-advanced-refund-system-for-woocommerce');?>
                </p>

            </div>
        </div>
    </div>
    <div class="two section section-odd clear">
        <div class="landing-container">
            <div class="col-2">
                <div class="section-title">
                    <img src="<?php echo YITH_WCARS_ASSETS_URL ?>/images/02-icon.png" />
                    <h2><?php _e('Accept all requests automatically','yith-advanced-refund-system-for-woocommerce');?></h2>
                </div>
                <p>
                    <?php echo sprintf(__('All incoming refund claims will be %1$sautomatically accepted and issued with an automatic payment%2$s through the same payment gateway used to buy.', 'yith-advanced-refund-system-for-woocommerce'), '<b>', '</b>');?>
                </p>
                <p>
                    <?php _e('In case there’s no payment gateway associated, you can keep managing refunds manually.', 'yith-advanced-refund-system-for-woocommerce');?>
                </p>
            </div>
            <div class="col-1">
                <img src="<?php echo YITH_WCARS_ASSETS_URL ?>/images/02.png" />
            </div>
        </div>
    </div>
    <div class="three section section-even clear">
        <div class="landing-container">
            <div class="col-1">
                <img src="<?php echo YITH_WCARS_ASSETS_URL ?>/images/03.png" />
            </div>
            <div class="col-2">
                <div class="section-title">
                    <img src="<?php echo YITH_WCARS_ASSETS_URL ?>/images/03-icon.png"/>
                    <h2><?php _e('Offer a coupon','yith-advanced-refund-system-for-woocommerce');?></h2>
                </div>
                <p>
                    <?php echo sprintf(__('A %1$sgood alternative%2$s to a refund could be offering a %1$sdiscount coupon%2$s to use on your shop. %3$s Users are happy and you don’t have to give them the money back.', 'yith-advanced-refund-system-for-woocommerce'), '<b>', '</b>', '<br>');?>
                </p>
            </div>
        </div>
    </div>
    <div class="four section section-odd clear">
        <div class="landing-container">
            <div class="col-2">
                <div class="section-title">
                    <img src="<?php echo YITH_WCARS_ASSETS_URL ?>/images/04-icon.png"  />
                    <h2><?php _e('Automatically restock products','yith-advanced-refund-system-for-woocommerce');?></h2>
                </div>
                <p>
                    <?php echo sprintf(__('A feature that is strictly related to the %1$sautomatic refund option%2$s: the plugin allows you to restock products automatically each time an automatic refund is issued.', 'yith-advanced-refund-system-for-woocommerce'), '<b>', '</b>');?>
                </p>
            </div>
            <div class="col-1">
                <img src="<?php echo YITH_WCARS_ASSETS_URL ?>/images/04.png"  />
            </div>
        </div>
    </div>
    <div class="five section section-even clear">
        <div class="landing-container">
            <div class="col-1">
                <img src="<?php echo YITH_WCARS_ASSETS_URL ?>/images/05.png" />
            </div>
            <div class="col-2">
                <div class="section-title">
                    <img src="<?php echo YITH_WCARS_ASSETS_URL ?>/images/05-icon.png"/>
                    <h2><?php _e('Allow refunds based on the order total','yith-advanced-refund-system-for-woocommerce');?></h2>
                </div>
                <p>
                    <?php echo sprintf(__('Refunds can be claimed only if the order total is above the minimum amount you have set: this allows reducing the number of refund requests.', 'yith-advanced-refund-system-for-woocommerce'), '<b>', '</b>');?>
                </p>
            </div>
        </div>
    </div>
    <div class="six section section-odd clear">
        <div class="landing-container">
            <div class="col-2">
                <div class="section-title">
                    <img src="<?php echo YITH_WCARS_ASSETS_URL ?>/images/06-icon.png"  />
                    <h2><?php _e('Allow file upload','yith-advanced-refund-system-for-woocommerce');?></h2>
                </div>
                <p>
                    <?php echo sprintf(__('Seeing the product is often necessary to grant a refund: the premium version of this plugin allows users %1$sto upload a file to their own refund request%2$s as a proof for their request legitimacy.', 'yith-advanced-refund-system-for-woocommerce'), '<b>', '</b>');?>
                </p>
            </div>
            <div class="col-1">
                <img src="<?php echo YITH_WCARS_ASSETS_URL ?>/images/06.png"  />
            </div>
        </div>
    </div>
    <div class="seven section section-even clear">
        <div class="landing-container">
            <div class="col-1">
                <img src="<?php echo YITH_WCARS_ASSETS_URL ?>/images/07.png" />
            </div>
            <div class="col-2">
                <div class="section-title">
                    <img src="<?php echo YITH_WCARS_ASSETS_URL ?>/images/07-icon.png"/>
                    <h2><?php _e('Configure refund settings on the single product','yith-advanced-refund-system-for-woocommerce');?></h2>
                </div>
                <p>
                    <?php _e('For each single product, whether it be simple or variable, you can enable or disable refunds, set up a deadline for new refund requests and customize the message shown on non-refundable products.', 'yith-advanced-refund-system-for-woocommerce');?>
                </p>
            </div>
        </div>
    </div>
    <div class="eight section section-odd clear">
        <div class="landing-container">
            <div class="col-2">
                <div class="section-title">
                    <img src="<?php echo YITH_WCARS_ASSETS_URL ?>/images/08-icon.png"  />
                    <h2><?php _e('Show a message on non-refundable products','yith-advanced-refund-system-for-woocommerce');?></h2>
                </div>
                <p>
                    <?php echo sprintf(__('%1$sCan\'t refund that specific product?%2$s Inform your users before they purchase it, and show a custom message on the product page.', 'yith-advanced-refund-system-for-woocommerce'), '<b>', '</b>');?>
                </p>
            </div>
            <div class="col-1">
                <img src="<?php echo YITH_WCARS_ASSETS_URL ?>/images/08.png"  />
            </div>
        </div>
    </div>
    <div class="section section-cta section-odd">
        <div class="landing-container">
            <div class="premium-cta">
                <p>
                    <?php echo sprintf( __('Upgrade to %1$spremium version%2$s of %1$sYITH Advanced Refund System for WooCommerce%2$s to benefit from all features!','yith-advanced-refund-system-for-woocommerce'),'<span class="highlight">','</span>' );?>
                </p>
                <a href="<?php echo $admin->get_premium_landing_uri(); ?>" target="_blank" class="premium-cta-button button btn">
                    <span class="highlight"><?php _e('UPGRADE','yith-advanced-refund-system-for-woocommerce');?></span>
                    <span><?php _e('to the premium version','yith-advanced-refund-system-for-woocommerce');?></span>
                </a>
            </div>
        </div>
    </div>
</div>