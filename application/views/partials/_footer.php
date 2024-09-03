<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<footer id="footer">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <div class="footer-top">
                    <div class="row">
                        <div class="col-12 col-md-3 footer-widget">
                            <div class="row-custom">
                                <div class="footer-logo">
                                    <a href="<?php echo lang_base_url(); ?>"><img src="<?php echo get_logo($this->general_settings); ?>" alt="logo"></a>
                                </div>
                            </div>
                            <div class="row-custom">
                                <div class="footer-about">
                                    <?= $this->settings->about_footer; ?>
                                </div>
                            </div>
                        </div>
                        <div class="col-12 col-md-3 footer-widget">
                            <div class="nav-footer">
                                <div class="row-custom">
                                    <h4 class="footer-title"><?php echo trans("footer_quick_links"); ?></h4>
                                </div>
                                <div class="row-custom">
                                    <ul>
                                        <li><a class="link-hover" href="<?php echo lang_base_url(); ?>"><?php echo trans("home"); ?></a></li>
                                        <?php if (!empty($this->menu_links)):
                                            foreach ($this->menu_links as $menu_link):
                                                if ($menu_link->location == 'quick_links'):
                                                    $item_link = generate_menu_item_url($menu_link);
                                                    if (!empty($menu_link->page_default_name)):
                                                        $item_link = generate_url($menu_link->page_default_name);
                                                    endif; ?>
                                                    <li><a class="link-hover" href="<?= $item_link; ?>"><?php echo html_escape($menu_link->title); ?></a></li>
                                                <?php endif;
                                            endforeach;
                                        endif; ?>
                                        <li><a class="link-hover" href="<?= generate_url('help_center'); ?>"><?= trans("help_center"); ?></a></li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <div class="col-12 col-md-3 footer-widget">
                            <div class="nav-footer">
                                <div class="row-custom">
                                    <h4 class="footer-title"><?php echo trans("footer_information"); ?></h4>
                                </div>
                                <div class="row-custom">
                                    <ul>
                                        <?php if (!empty($this->menu_links)):
                                            foreach ($this->menu_links as $menu_link):
                                                if ($menu_link->location == 'information'):
                                                    $item_link = generate_menu_item_url($menu_link);
                                                    if (!empty($menu_link->page_default_name)):
                                                        $item_link = generate_url($menu_link->page_default_name);
                                                    endif; ?>
                                                    <li><a class="link-hover" href="<?= $item_link; ?>"><?php echo html_escape($menu_link->title); ?></a></li>
                                                <?php endif;
                                            endforeach;
                                        endif; ?>

                                        <?php if (!empty($this->menu_links)):
                                            foreach ($this->menu_links as $menu_link):
                                                if ($menu_link->location == 'information'):?>
                                                <?php endif;
                                            endforeach;
                                        endif; ?>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <div class="col-12 col-md-3 footer-widget">
                            <div class="row">
                                <div class="col-12">
                                    <h4 class="footer-title"><?php echo trans("follow_us"); ?></h4>
                                    <div class="footer-social-links">
                                        <!--include social links-->
                                        <?php $this->load->view('partials/_social_links', ['show_rss' => true]); ?>
                                    </div>
                                </div>
                            </div>
                            <?php if ($this->general_settings->newsletter_status == 1): ?>
                                <div class="row">
                                    <div class="col-12">
                                        <div class="newsletter">
                                            <div class="widget-newsletter">
                                                <h4 class="footer-title"><?= trans("newsletter"); ?></h4>
                                                <form id="form_newsletter_footer" class="form-newsletter">
                                                    <div class="newsletter">
                                                        <input type="email" name="email" class="newsletter-input" maxlength="199" placeholder="<?php echo trans("enter_email"); ?>" required>
                                                        <button type="submit" name="submit" value="form" class="newsletter-button"><?php echo trans("subscribe"); ?></button>
                                                    </div>
                                                    <input type="text" name="url">
                                                    <div id="form_newsletter_response"></div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="container-fluid">
        <div class="row">
            <div class="footer-bottom">
                <div class="container">
                    <div class="copyright">
                        <?php echo html_escape($this->settings->copyright); ?>
                    </div>
                    <div class="footer-payment-icons">
                        <img src="data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7" data-src="<?php echo base_url(); ?>assets/img/payment/visa.svg" alt="visa" class="lazyload">
                        <img src="data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7" data-src="<?php echo base_url(); ?>assets/img/payment/mastercard.svg" alt="mastercard" class="lazyload">
                        <img src="data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7" data-src="<?php echo base_url(); ?>assets/img/payment/maestro.svg" alt="maestro" class="lazyload">
                        <img src="data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7" data-src="<?php echo base_url(); ?>assets/img/payment/amex.svg" alt="amex" class="lazyload">
                        <img src="data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7" data-src="<?php echo base_url(); ?>assets/img/payment/discover.svg" alt="discover" class="lazyload">
                    </div>
                </div>
            </div>
        </div>
    </div>
</footer>
<?php if (!isset($_COOKIE["modesy_cookies_warning"]) && $this->settings->cookies_warning): ?>
    <div class="cookies-warning">
        <div class="text"><?php echo $this->settings->cookies_warning_text; ?></div>
        <a href="javascript:void(0)" onclick="hide_cookies_warning();" class="icon-cl"> <i class="icon-close"></i></a>
    </div>
<?php endif; ?>
<a href="javascript:void(0)" class="scrollup" style="z-index:100!important;"> <i class="icon-arrow-up" style="z-index:100!important;"></i></a>
<script src="https://js.pusher.com/7.2/pusher.min.js"></script>
<script src="<?= base_url(); ?>assets/js/jquery-3.5.1.min.js"></script>
<script src="<?= base_url(); ?>assets/js/easy.qrcode.min.js"></script>
<script src="<?= base_url(); ?>assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="<?= base_url(); ?>assets/js/plugins-2.1.js"></script>
<script src="<?= base_url(); ?>assets/js/script-2.1.js"></script>
<script src="<?= base_url(); ?>assets/js/messenger.js"></script>
<?php if (!empty($this->session->userdata('mds_send_email_data'))): ?>
    <script>$(document).ready(function () {
            var data = JSON.parse(<?= json_encode($this->session->userdata("mds_send_email_data"));?>);
            if (data) {
                data[mds_config.csfr_token_name] = $.cookie(mds_config.csfr_cookie_name);
                data["sys_lang_id"] = mds_config.sys_lang_id;
                $.ajax({
                    type: "POST", url: "<?= base_url(); ?>mds-send-email-post", data: data, success: function (response) {
                    }
                });
            }
        });</script>
<?php endif;
$this->session->unset_userdata('mds_send_email_data'); ?>
<script>$('<input>').attr({type: 'hidden', name: 'sys_lang_id', value: '<?= $this->selected_lang->id; ?>'}).appendTo('form[method="post"]');</script>
<script>
    <?php if (!empty($index_categories)):foreach ($index_categories as $category):?>
    if ($('#category_products_slider_<?= $category->id; ?>').length != 0) {
        $('#category_products_slider_<?= $category->id; ?>').slick({autoplay: false, autoplaySpeed: 4900, infinite: true, speed: 200, swipeToSlide: true, rtl: mds_config.rtl, cssEase: 'linear', prevArrow: $('#category-products-slider-nav-<?= $category->id; ?> .prev'), nextArrow: $('#category-products-slider-nav-<?= $category->id; ?> .next'), slidesToShow: 5, slidesToScroll: 1, responsive: [{breakpoint: 992, settings: {slidesToShow: 4, slidesToScroll: 1}}, {breakpoint: 768, settings: {slidesToShow: 3, slidesToScroll: 1}}, {breakpoint: 576, settings: {slidesToShow: 2, slidesToScroll: 1}}]});
    }
    <?php endforeach;
    endif; ?>
    <?php if ($this->general_settings->pwa_status == 1): ?>
    if ('serviceWorker' in navigator) {
        window.addEventListener('load', function () {
            navigator.serviceWorker.register('<?= base_url();?>pwa-sw.js').then(function (registration) {
            }, function (err) {
                console.log('ServiceWorker registration failed: ', err);
            }).catch(function (err) {
                console.log(err);
            });
        });
    } else {
        console.log('service worker is not supported');
    }
    <?php endif; ?>
</script>
<?php if (!empty($video) || !empty($audio)): ?>
    <script src="<?= base_url(); ?>assets/vendor/plyr/plyr.min.js"></script>
    <script src="<?= base_url(); ?>assets/vendor/plyr/plyr.polyfilled.min.js"></script>
    <script>const player = new Plyr('#player');
        $(document).ajaxStop(function () {
            const player = new Plyr('#player');
        });
        const audio_player = new Plyr('#audio_player');
        $(document).ajaxStop(function () {
            const player = new Plyr('#audio_player');
        });
        $(document).ready(function () {
            setTimeout(function () {
                $(".product-video-preview").css("opacity", "1");
            }, 300);
            setTimeout(function () {
                $(".product-audio-preview").css("opacity", "1");
            }, 300);
        });</script>
<?php endif; ?>
<?php if (!empty($load_support_editor)):
    $this->load->view('support/_editor');
endif; ?>
<?php if (check_newsletter_modal($this)): ?>
    <script>$(window).on('load', function () {
            $('#modal_newsletter').modal('show');
        });</script>
<?php endif; ?>
<?= $this->general_settings->google_analytics; ?>
<?= $this->general_settings->custom_javascript_codes; ?>

<script>
window.addEventListener('load', ()=>{checkCookie()});
// Get the modal
var modal = document.getElementById("qrpopup");

// Get the button that opens the modal
var btn = document.getElementById("qrpopup-btn");
var btn2 = document.querySelectorAll("#qrpopup-btn2");
var image;
var slug
var shopName;
// Get the <span> element that closes the modal
var span = document.getElementsByClassName("close")[0];

// When the user clicks the button, open the modal
if(btn != null){
    btn.onclick = function() {
        var image = btn.getAttribute('src')
        var slug = btn.getAttribute('data-slug')
        var shopName = btn.getAttribute('alt')
        // modal.style.display = "flex";
        // modal.style.zIndex = 99;
        Qrcode(slug, image, shopName)
    }
} 
var qrcode
btn2.forEach(button => {
    button.addEventListener('click', () => {
       var image = button.getAttribute('src')
       var slug = button.getAttribute('data-slug')
       var shopName = button.getAttribute('alt')
    // select the popup element and show it
        //modal.style.display = "flex";
       
        Qrcode(slug, image,shopName)
    });
});

document.getElementById("qrPopup").addEventListener('click', function(event) {
  if (event.target == document.getElementById("qrPopup")) {
        var oldcanv = document.getElementsByTagName('canvas');
        var odld = document.getElementById('qrcode');
        odld.removeChild(oldcanv[0])
        image = null;
        slug = null;
        qrcode = null;
    }
});

function QrWithLogo(slugs, images, shopName){
    // create Qrcode with easy QR library
    var qrcode   = new QRCode(document.getElementById("qrcode"), {
        text: slugs,
        width:280,
        height:280,
        Border: '#000',
    });
    // create the canvas 2d
    const canvas = document.getElementsByTagName('canvas');
    const ctx = canvas[0].getContext('2d');
    // create the image
    const image = new Image();
    image.src = images;
    image.onload = function() {
        const canvasWidth = canvas[0].width;
        const canvasHeight = canvas[0].height;
        const imageSize = 80; // Size of the resized image

        const imageX = canvasWidth / 2 - imageSize / 2; // to center the image horizontally
        const imageY = canvasHeight / 2 - imageSize / 2; // to center the image vertically
        ctx.beginPath();
        // make the image round
        ctx.arc(
        imageX + imageSize / 2,
        imageY + imageSize / 2,
        imageSize / 2,
        0,
        2 * Math.PI
        );
        ctx.closePath();
        ctx.clip();
        // draw image
        ctx.drawImage(image, imageX, imageY, imageSize, imageSize);
       
    }
}

// download the image
function downloadQr(shopName){
    linkDownload = document.getElementById("download-qr");
    linkDownload.addEventListener('click', ()=>
  {
    canvass = document.getElementsByTagName('canvas')
            // Set the border size
        const borderSize = 10;
    // canvass[0].toDataURL()
    // var link = document.createElement('a');
    const newCanvas = document.createElement("canvas") ;
        newCanvas.width = canvass[0].width +  borderSize * 2;
        newCanvas.height = canvass[0].height + borderSize * 2;
        const context = newCanvas.getContext("2d");
        context.fillStyle = "#fff";
        context.fillRect(0, 0, newCanvas.width, newCanvas.height);
        context.drawImage(canvass[0], borderSize, borderSize);

    linkDownload.href = newCanvas.toDataURL("image/jpeg"); // Convert canvas to data URL
    linkDownload.download = shopName+".jpeg"; // Set download file name
    console.log(newCanvas.width)
    
  })
}

// generate Qrcode 
function Qrcode(slugs, images, shopName){
    QrWithLogo(slugs, images, shopName)
    downloadQr(shopName);
}
function MapQrcode(el){
    var image = el.getAttribute('src')
    var slug = el.getAttribute('data-slug')
    var shopName = el.getAttribute('alt')
    QrWithLogo(slug, image, shopName)
    downloadQr(shopName);
}
//auth butn link if No
    let fullPageBox = document.getElementById('fullpage-box');
    let linkBtn = document.getElementById('link-btn');

    linkBtn.addEventListener('click', ()=> {
        location.href = "https://www.instagram.com/pluglistapp/";
       
    });

    
     
    ageVerificationBtn = document.getElementById('over-age');

    ageVerificationBtn.addEventListener('click', ()=> {
        const d = new Date();
     d.setTime(d.getTime() + (20 * 24 * 60 * 60 * 1000));
    let expires = "expires="+d.toUTCString();
    document.cookie = "ageOver=YES;" + expires + ";path=/";
    fullPageBox.style.display = 'none';
       
    });

    function getCookie(cname) {
        let name = cname + "=";
        let decodedCookie = decodeURIComponent(document.cookie);
        let ca = decodedCookie.split(';');
        for(let i = 0; i < ca.length; i++) {
            let c = ca[i];
            while (c.charAt(0) == ' ') {
            c = c.substring(1);
            }
            if (c.indexOf(name) == 0) {
            return c.substring(name.length, c.length);
            }
        }
        return "";
    }
    function checkCookie() {
        
        let age = getCookie("ageOver");
        if (age != "") {
            fullPageBox.style.display = 'none';
        } else {
            fullPageBox.style.display = 'flex';
        }
    }    
    if(document.getElementById('input_vendor_files')){
    var fileInput = document.getElementById('input_vendor_files');
        let selectedFiles = [];

        fileInput.addEventListener('change', (event) => {
            const newFiles = Array.from(event.target.files);
            selectedFiles = selectedFiles.concat(newFiles);

            // Update both the file list and the input field
            updateInputField();
        });
        function updateInputField() {
    // Create a new FileList object containing selectedFiles
    const newFileList = new DataTransfer();
    selectedFiles.forEach((file) => {
        newFileList.items.add(file);
    });

    // Set the new FileList to the input field
    fileInput.files = newFileList.files;
}
    }
const toggleNumber = ()=>{
    document.getElementById("phone_number_text").classList.toggle('display-none');
    document.getElementById("phone_number").classList.toggle("display-none");
};

</script>

<script>
  
//   Pusher.logToConsole = true;

// var pusher = new Pusher('b46af13a222b64f2fe66', {
//   cluster: 'ap1'
// });
// var user_id;
// user_id = <?php echo (!auth_check()) ? 'null' : $this->auth_user->id; ?>;
// var channel = pusher.subscribe('pusherChat');

// channel.bind('my-event', function(messageData) {

       
//     if( user_id == messageData.data.senderId){
//         message_box = '<div class="message-list-item">'+
//                             '<div class="message-list-item-row-sent">'+
//                                 '<div class="user-message">'+
//                                     '<div class="message-text" style="background-color:#FA7348">'+
//                                     messageData.data.message+
//                                     '</div>'+
//                                     '<span class="time timeStamp" >Just Now</span>'+
//                                 '</div>'+
//                                ' <div class="user-avatar">'+
//                                    ' <div class="message-user">'+
//                                         '<a href="'+messageData.data.senderSlug+'"> '+
//                                             '<img src="'+messageData.data.senderImage +'" alt="" class="img-profile">'+
//                                         '</a>'+
//                                     '</div>'+
//                                 '</div>'+
//                             '</div>'+
//                         '</div>';
                        
//     }
//     else{
//         message_box = '<div class="message-list-item">'+
//                     '<div class="message-list-item-row-received">'+
//                                 '<div class="user-avatar">'+
//                                     '<div class="message-user">'+
//                                         '<a href="'+messageData.data.senderSlug  +'">'+
//                                             '<img src="'+messageData.data.senderImage +'" alt="" class="img-profile">'+
//                                        ' </a>'+
//                                    ' </div>'+
//                                ' </div>'+
//                                ' <div class="user-message">'+
//                                    ' <div class="message-text">'+messageData.data.message+''+
//                                     '</div>'+
//                                     '<span class="time timeStamp" >Just Now</span>'+
//                                 '</div>'+
//                            ' </div>'+
//                         '</div>'
//     }
//     console.log($('#message-custom-scrollbar'));

//     $('#message-custom-scrollbar .os-content').append(message_box);
//     $("#message-custom-scrollbar .os-content").animate({ scrollTop: 20000000 }, "slow");
// });

// $("#theform").submit(function(e){
//     e.preventDefault();
//     console.log("this is tyuity")
//     var textareaValue = document.getElementById("myTextarea").value.trim();
//     if (textareaValue === "") {
//         document.getElementById("myTextarea").style.borderColor="rgba(220,53,69,0.40)"
//         return false; // Prevent form submission
//     }
//     else{
//         // const d = new Date();
//         //     const months = ["1", "2", "3", "4", "5", "6", "7", "8", "9", "10", "11", "12"];
//         //   let month = months[d.getMonth()];
//         //   let day = months[d.getDay()];
//         //   Hour = d.getHours()
//         //   minutes =d.getMinutes()
//         //   datetime =  Hour+":"+minutes+" "+ day+"/"+month+"/"+d.getFullYear();
//         $.post('https://erickci.staging-server.online/send-message-post',{
//                 csrf_mds_token : $('input[name="csrf_mds_token"]').val(),
//                 senderId: user_id,
//                 receiver_id:$('input[name="receiver_id"]').val(),
//                 conversation_id:$('input[name="conversation_id"]').val(),
//                 message:textareaValue,
//                 // datetime:datetime,
//             },function(response){
//                 //console.log(response+"this is resposed")
//                 document.getElementById("myTextarea").value = '';
//             }
//         );
//     }
//     //   alert("Submitted");
// });
// $(document).ready(function(){

//     $('#form_messages').submit(function(event){
//         event.preventDefault()
//             alert("Submitted");
     
//       const months = ["1", "2", "3", "4", "5", "6", "7", "8", "9", "10", "11", "12"];
//       console.log("this is resposed")
//       const d = new Date();
//       let month = months[d.getMonth()];
//       let day = months[d.getDay()];
//       Hour = d.getHours()
//       minutes =d.getMinutes()
//       datetime =  Hour+":"+minutes+" "+ day+"/"+month+"/"+d.getFullYear();

//       $.post('http://localhost/pusherchat/functions/sendmessge.php',{
//         senderId: senderId,
//         receiver_id:$('#receiver_id'),
//         message:$('#textarea'),
//         datetime:datetime,
//       },function(response){
//         console.log(response+"this is resposed")
//       });
//       $("#textarea").val('');
//   })
//   });


function validateFormSpace() {
    var textarea= document.getElementById("myTextarea")
  var textareaValue = document.getElementById("myTextarea").value.trim();

  if (textareaValue === "") {
    textarea.style.borderColor="rgba(220,53,69,0.40)"
    return false; // Prevent form submission
  }

  return true; // Allow form submission
}

// btn shop details form validate
$(document).on('click', '.btn-shop-details', function(e){
    e.preventDefault();
    let form = $(this).closest('form'); 
    let phone_filter = /^\d*(?:\.\d{1,2})?$/;
    let shop_name = form.find('input[name="shop_name"]').val();
    let first_name = form.find('input[name="first_name"]').val();
    let last_name = form.find('input[name="last_name"]').val();
    let phone_number = form.find('input[name="phone_number"]').val();
    let select_countries = form.find('#select_countries option:selected').val();
    let select_states = form.find('#select_states option:selected').val();
    let select_cities = form.find('#select_cities option:selected').val();
    let about_me = form.find('textarea[name="about_me"]').val();
    let address = form.find('input[name="address"]').val();
    let zip_code = form.find('input[name="zip_code"]').val();
    let terms_conditions = form.find('input[name="terms_conditions"]').is(":checked");
    let is_validation = true;

    // alert(about_me);
    if(typeof shop_name== 'undefined' || shop_name=='' || shop_name==null){
        form.find('input[name="shop_name"]').removeClass('valid').addClass('error');
        is_validation = false;
    }else{
        form.find('input[name="shop_name"]').removeClass('error').addClass('valid');
        is_validation = true;
    }
    if(typeof first_name== 'undefined' || first_name=='' || first_name==null){
        form.find('input[name="first_name"]').removeClass('valid').addClass('error');
        is_validation = false;
    }else{
        form.find('input[name="first_name"]').removeClass('error').addClass('valid');
        is_validation = true;
    }
    if(typeof last_name== 'undefined' || last_name=='' || last_name==null){
        form.find('input[name="last_name"]').removeClass('valid').addClass('error');
        is_validation = false;
    }else{
        form.find('input[name="last_name"]').removeClass('error').addClass('valid');
        is_validation = true;
    }
    if(typeof phone_number== 'undefined' || phone_number=='' || phone_number==null){
        form.find('input[name="phone_number"]').removeClass('valid').addClass('error');
        is_validation = false;
    }else if (!phone_filter.test(phone_number)) {
        form.find('input[name="phone_number"]').removeClass('valid').addClass('error');
        is_validation = false;
    }else{
        form.find('input[name="phone_number"]').removeClass('error').addClass('valid');
        is_validation = true;
    }
    if(typeof select_countries=='undefined' || select_countries=='' || select_countries==null){
        form.find('.locations-fields .select2-selection--single').removeClass('valid').addClass('error');
        is_validation = false;
    }else{
        form.find('.locations-fields .select2-selection--single').removeClass('error').addClass('valid');
        is_validation = true;
    }
    if(typeof select_states=='undefined' || select_states=='' || select_states==null){
        form.find('.locations-fields #get_states_container .select2-selection--single').removeClass('valid').addClass('error');
        is_validation = false;
    }else{
        form.find('.locations-fields #get_states_container .select2-selection--single').removeClass('error').addClass('valid');
        is_validation = true;
    }
    if(typeof select_cities=='undefined' || select_cities=='' || select_cities==null){
        form.find('.locations-fields #get_cities_container .select2-selection--single').removeClass('valid').addClass('error');
        is_validation = false;
    }else{
        form.find('.locations-fields #get_cities_container .select2-selection--single').removeClass('error').addClass('valid');
        is_validation = true;
    }
    if(typeof address=='undefined' || address=='' || address==null){
        form.find('input[name="address"]').removeClass('valid').addClass('error');
        is_validation = false;
    }else{
        form.find('input[name="address"]').removeClass('error').addClass('valid');
        is_validation = true;
    }
    if(typeof zip_code=='undefined' || zip_code=='' || zip_code==null){
        form.find('input[name="zip_code"]').removeClass('valid').addClass('error');
        is_validation = false;
    }else{
        form.find('input[name="zip_code"]').removeClass('error').addClass('valid');
        is_validation = true;
    }
    if(typeof about_me=='undefined' || about_me=='' || about_me==null){
        form.find('textarea[name="about_me"]').removeClass('valid').addClass('error');
        is_validation = false;
    }else{
        form.find('textarea[name="about_me"]').removeClass('error').addClass('valid');
        is_validation = true;
    }
    if(terms_conditions==false){
        $('.parent-terms-conditions').find('.text-danger').remove()
        $('.parent-terms-conditions').append("<p class='text-danger'>You have to accept the terms!</p>");
        form.find('.parent-terms-conditions').removeClass('valid').addClass('custom-control-validate-error');
        is_validation = false;
    }else{
        $('.parent-terms-conditions').find('.text-danger').remove();
        form.find('.parent-terms-conditions').removeClass('custom-control-validate-error').addClass('valid');
        is_validation = true;
    }
    
    if(is_validation){
        //alert('successfully submit');
        //return;
        form.submit();
    }

});

// inputField = document.querySelector('input[ name="username"]');
// registerbtn = document.querySelector('#register')
// if(inputField){
   
//     inputField.addEventListener('input', () => {
//         const value = inputField.value;
//         const containsSpace = /\s/.test(value);
//         const containsCapitalLetter = /[A-Z]/.test(value);
//         const containsSpecialChar = /[^\w\s]/.test(value);
//         if (!containsSpace && !containsCapitalLetter && !containsSpecialChar) {
//             // Input is valid
//             document.getElementById('errordisplay').classList.add('errordisplay');
//             registerbtn.disabled  = false;
           
//             // inputField.classList.remove('error');
//         } else {
//             // Input is invalid
         
//             document.getElementById('errordisplay').classList.remove('errordisplay');
//             registerbtn.disabled  = true;
        
//             // inputField.classList.add('error');
//         }
//     });
// }
// Get the current time
// var startTime = new Date();
// setInterval(updateMessage, 60000);

// // Function to update the message
// function updateMessage() {
//   var currentTime = new Date();
//   var minutesPassed = Math.floor((currentTime - startTime) / 60000);
//   if (minutesPassed < 1) {
//     //console.log("Just now");
//   }
//   else if (minutesPassed === 1) {
//     var elements = document.getElementsByClassName('timeStamp');
//     var elementsArray = [...elements];
//     elementsArray.forEach(function(element) {
//         element.innerHTML = '1 minute ago';
//         });
//     //console.log("1 minute ago");
//   } else {
//     var elements = document.getElementsByClassName('timeStamp');
//     var elementsArray = [...elements];
//     elementsArray.forEach(function(element) {
//         element.innerHTML = minutesPassed + " minutes ago";
//         });
//     //console.log(minutesPassed + " minutes ago");
//   }
// }



// <?php //if(auth_check()){ ?>

// $("#singleForm").submit(function(e){
//     e.preventDefault();
//     console.log("this is tyuity")
//     var textareaValue = document.getElementById("myTextarea").value.trim();
//     if (textareaValue === "") {
//         document.getElementById("myTextarea").style.borderColor="rgba(220,53,69,0.40)"
//     return false; // Prevent form submission
//   }
//   else{
//     // var newValue = textareaValue.replace(/\n/g, "<br>");
//     $.post('<?= base_URL()?>send-new-message-post',{
//         csrf_mds_token : $('input[name="csrf_mds_token"]').val(),
//         senderId: <?php // $this->auth_user->id ?>,
//         receiver_id:$('input[name="receiver_id"]').val(),
//         conversation_id:$('input[name="conversation_id"]').val(),
//         message:textareaValue,
//         // datetime:datetime,
//       },function(response){
//         dispalySentMessages(textareaValue)
//         document.getElementById("myTextarea").value = '';
//         //console.log(response)
//       });
//     }
//     //   alert("Submitted");
//   });



// // Function to send a long polling request to the server
// function longPoll(last_message = null,last_conversation_id_func) {
//     let last_message_id = last_message;
//     let last_conversations_id;
//     last_conversations_id = last_conversation_id_func
//   const xhr = new XMLHttpRequest();
//   xhr.onreadystatechange = function () {
//     if (xhr.readyState === XMLHttpRequest.DONE) {
        
//       if (xhr.status === 200) {
        
//         const response = JSON.parse(xhr.responseText);
//         new_conver_id =response.last_conversation_id
//        last_conversations_id = response.last_conversation_id;
//        // console.log(response)
//        // console.log(last_conversations_id)
//         // Process received messages
//         displaySidebar(response)
//         response.messages.forEach((message) => {
//           // Display the message in the chat container
//           dispalyMessages(message,response)
//           last_message_id =message.id;
          
       
//         });
        
       
//         //console.log("adfadrs"+last_conversations_id)
       
//         // Get the timestamp of the last message
//         // const lastMessage = response.messages[response.messages.length - 1];
//         // const lastMessageTimestamp = lastMessage ? lastMessage.timestamp : null;
        
//         // Initiate another long polling request recursively with the last message timestamp
//         setTimeout(()=>{longPoll(last_message_id ,new_conver_id)}, 6000);
        
//       }

//     }
//   };
//   const formData = new FormData();

// // Append the last timestamp to the FormData object
// formData.append('user_id', <?php //echo $user_id?>);
// if(<?php //echo $last_message_id?> != last_conversations_id){
//     formData.append('last_conversation_id', last_conversations_id);
// }
// else{
//     formData.append('last_conversation_id', <?php //echo $last_message_id?>);
// }
// if(!last_message_id){
//     formData.append('last_message_id', <?php //echo $last_message_id?>);
//    // console.log( last_message_id +"this is null  <?php //echo $last_message_id?>")
   
//     formData.append('sender_id', <?php //echo $user_id?>);
// }
// else{
//     formData.append('last_message_id', last_message_id);
//     formData.append('sender_id', <?php //echo $user_id?>);
//     //console.log( last_message_id +"this is not null  <?php //echo $last_message_id?>")
// }

// formData.append(mds_config.csfr_token_name,$.cookie(mds_config.csfr_cookie_name));
// //console.log(mds_config.csfr_token_name,$.cookie(mds_config.csfr_cookie_name))
//   // Append the last message timestamp to the request URL
//   const url = '<?= base_URL()?>messages-new';
//   xhr.open('POST', url);
//   xhr.send(formData);
// }
// longPoll(null,<?php //echo $last_message_id?>);

// function dispalyMessages(messages,response){
//     var message_content = messages.content.replace(/\n/g, "<br>")
//   //console.log("dispaly messae to receiver")
//     if( <?php // $this->auth_user->id ?> == messages.receiver_id){
//         //console.log("dispaly messae to receiver id condition")
//         message_box = '<div class="message-list-item">'+
//                     '<div class="message-list-item-row-received">'+
//                                 '<div class="user-avatar">'+
//                                     '<div class="message-user">'+
//                                         '<a href="'+response.sender_user.user_profile_link+'">'+
//                                             '<img src="'+response.sender_user.avatar+'" alt="" class="img-profile">'+
//                                        ' </a>'+
//                                    ' </div>'+
//                                ' </div>'+
//                                ' <div class="user-message">'+
//                                    ' <div class="message-text">'+message_content+''+
//                                     '</div>'+
//                                     '<span class="time timeStamp" >Just Now</span>'+
//                                 '</div>'+
//                            ' </div>'+
//                         '</div>'
//                         $('#message-custom-scrollbar .os-content').append(message_box);

//                         if ($('#message-custom-scrollbar').length > 0) {
//                             var instance_message_scrollbar = OverlayScrollbars(document.getElementById('message-custom-scrollbar'), {});
//                             instance_message_scrollbar.scroll({y: "100%"}, 0);
//                         }
//     }
  
  
// }
// function displaySidebar(response){
   
//     if(response.new_user_conversation.role_id == 2 || response.new_user_conversation.role_id == 1){
//         if( response.new_user_conversation.shop_name){
//             icon = ''
//             if(acc_type == 2){
//                 icon = '<i class="icon-verified icon-verified-member" style="color:orange"></i>'
//             }
//             else{
//                 icon = '<i class="icon-verified icon-verified-member" ></i>'
//             }
//             $name = '<strong class="username link-hover" style="font-weight:600">'+response.new_user_conversation.shop_name+'</strong>'+icon+' </div>'
//         }
//         else{
//         $name = '<strong class="username link-hover" style="font-weight:600">'+response.new_user_conversation.username+'</strong>'+icon+'  </div>'
//         }
//     }
//     else{
//         $name = '<strong class="username link-hover" style="font-weight:600">'+response.new_user_conversation.first_name+'</strong> </div>'
//     }
//     sidebar_box = '<div class="conversation-item">'+
//     '<a href="<?= base_URL()?>messages_view/conversation/'+response.last_conversation_id+'" class="conversation-item-link">'+
//     '<div class="middle"> <img src="<?= base_URL()?>'+response.new_user_conversation.avatar+'" alt="'+response.new_user_conversation.shop_name+'"></div>'+
//     '<div class="right"><div class="row-custom">'+
//     $name+
//     '<div class="row-custom m-b-0"></div></div></a> </div>'
//     if(typeof response.new_user_conversation === 'object'){
//         $('#message-sidebar-custom-scrollbar-id .os-content').append(sidebar_box);
//     }
//     else{
//         //console.log("empty")
//     }
// }
// function dispalySentMessages(textareaValue){
//     //console.log("dispaly messae to sender")
//     var message_content = textareaValue.replace(/\n/g, "<br>")
//  // if( <?php // $this->auth_user->id ?> == messages.receiver_id){
//       message_box = '<div class="message-list-item">'+
//                           '<div class="message-list-item-row-sent">'+
//                               '<div class="user-message">'+
//                                   '<div class="message-text" style="background-color:#FA7348">'+
//                                   message_content+
//                                   '</div>'+
//                                   '<span class="time timeStamp" >Just Now</span>'+
//                               '</div>'+
//                              ' <div class="user-avatar">'+
//                                  ' <div class="message-user">'+
//                                       '<a href="<?php // generate_profile_url($this->auth_user->slug) ?>"> '+
//                                           '<img src="<?php //echo get_user_avatar($this->auth_user); ?>" alt="" class="img-profile">'+
//                                       '</a>'+
//                                   '</div>'+
//                               '</div>'+
//                           '</div>'+
//                       '</div>';

//                       $('#message-custom-scrollbar .os-content').append(message_box);
                      
//                       if ($('#message-custom-scrollbar').length > 0) {
//                             var instance_message_scrollbar = OverlayScrollbars(document.getElementById('message-custom-scrollbar'), {});
//                             instance_message_scrollbar.scroll({y: "100%"}, 0);
//                         }
                      
//  // }
// }
// <?php //} ?>

// var textarea = document.getElementById("myTextarea");
// textarea.addEventListener("keyup", function(event){
//     if ((event.keyCode === 13 && event.ctrlKey) || (event.keyCode === 13 && event.metaKey)) {
//         // values = textarea.value.trim()
//         // textarea.value = values +"\r\n"
//         // console.log( textarea.value );
//         document.getElementById("submitBtn").click();
//     }
//     if (event.keyCode === 13 ) {
//         var textareaValues = document.getElementById("myTextarea").value.trim();
//         if(values = ''){
//             textarea.value =values
//             //console.log( textareaValues);
//         }
//         else{
//             textarea.value = textareaValues +"\n"
//             //console.log( textarea.value );
//         }
       
//         // document.getElementById("submitbtn").click();
//     }
// })
// window.addEventListener("load", ()=>{
//     document.getElementById("myTextarea").focus()
// })
<?php if($this->auth_check): ?>
alertDiv = document.getElementById("alert-div");
document.getElementById("alert-btn").addEventListener('click',()=>{
    console.log("Asdf")
    if(alertDiv.classList.contains("display-none")){
        document.getElementById("navbar_mobile_categories").style.display="none"
        document.getElementById("navbar_mobile_links").style.display="none"
        alertDiv.classList.remove("display-none")
    }
    else{
        document.getElementById("navbar_mobile_categories").style.display="block"
        document.getElementById("navbar_mobile_links").style.display="block"
        alertDiv.classList.add("display-none")
    }
})
document.getElementById("alert-go-back").addEventListener('click',()=>{
    if(!alertDiv.classList.contains("display-none")){
        alertDiv.classList.add("display-none")
        document.getElementById("navbar_mobile_categories").style.display="block"
        document.getElementById("navbar_mobile_links").style.display="block"
        document.getElementById("navbar_mobile_categories").classList.add('slide-in-150s')
        document.getElementById("navbar_mobile_links").classList.add('slide-in-150s')
        alertDiv.addEventListener('transitionend',()=>{
            console.log("asdf")
            document.getElementById("navbar_mobile_links").classList.remove('slide-in-150s')
            document.getElementById("navbar_mobile_categories").classList.remove('slide-in-150s')
        })
        

       
    }
    else{
        alertDiv.classList.remove("display-none")
        document.getElementById("navbar_mobile_categories").style.display="none"
        document.getElementById("navbar_mobile_links").style.display="none"
       
    }
})
let notificationTimeOut = null
function deleteNotification(e,id){
    e.stopPropagation();
    element = document.getElementById("notification-"+id)
    element.remove();
    
    m_element = document.getElementById("m-notification-"+id)
    m_element.remove();
    deleteNotificationAjax(id)
    

}
function clearTheTimeOut(){
    clearTimeout(notificationTimeOut);
}

function deleteNotificationAjax(id) {

    const xhr = new XMLHttpRequest();
    xhr.onreadystatechange = function () {
        if (xhr.readyState === XMLHttpRequest.DONE) {

            if (xhr.status === 200) {


                const response = JSON.parse(xhr.responseText);
                if(response.status == true){
                    const notificationList = document.getElementById("notification-list");
                    const mNotificationList = document.getElementById("m-notification-list");
    
                    if (notificationList.childElementCount === 0 || mNotificationList.childElementCount === 0) {
                        notificationTimeOut=  setTimeout(() => {
                            emptyItem = document.createElement('li')
                            emptyItem.style.padding = "4px 15px"
                            emptyItem.innerHTML = "No Notification Right Now"

                            MobemptyItem = document.createElement('li')
                            MobemptyItem.classList = "nav-item"
                            MobemptyItem.innerHTML = '<a href="javascript:void(0)" class="nav-link">No Notification Right Now</a>'

                            notificationList.appendChild(emptyItem)
                            mNotificationList.appendChild(MobemptyItem)
                            clearTheTimeOut()
                        }, 100); 
                    }
                
                }
                else{
                    alert("can not delete notification")
                }
            

            }
        }
    }
    const formData = new FormData();
    formData.append(mds_config.csfr_token_name, $.cookie(mds_config.csfr_cookie_name));
    formData.append("notification_id", id);
    xhr.open('POST', mds_config.base_url + 'delete-notification');
    xhr.send(formData);

}


<?php endif;?>
let start = null
let coiii = 0
if(  document.getElementById('map-section')){
    document.getElementById('map-section').addEventListener('mousedown', ()=>{

document.getElementById('map-section').style.height = '100vh'

})
document.getElementById('mobile-vw-section').addEventListener('mousedown', ()=>{
document.getElementById('map-section').style.height = '40vh'
})


}

</script>
</body>
</html>
