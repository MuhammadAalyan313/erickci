<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<style>

</style>
<div id="wrapper" style="padding: 0;">
  <div class="shop-container">
    <section class="map-container" id="map-container">
      <!-- <script async
             src="https://maps.googleapis.com/maps/api/js?key=AIzaSyAG03HmQeEmm_CNyhG5zT7OrIJHdmcW5nU&callback=initMap">
      </script>  -->
      <!-- <script src="https://polyfill.io/v3/polyfill.min.js?features=default"></script> -->
      <!-- <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyAG03HmQeEmm_CNyhG5zT7OrIJHdmcW5nU&libraries=visualization"></script> -->
      <script src="https://developers.google.com/maps/documentation/javascript/examples/markerclusterer/markerclusterer.js"></script>
      <script>
        (g => {
          var h, a, k, p = "The Google Maps JavaScript API",
            c = "google",
            l = "importLibrary",
            q = "__ib__",
            m = document,
            b = window;
          b = b[c] || (b[c] = {});
          var d = b.maps || (b.maps = {}),
            r = new Set,
            e = new URLSearchParams,
            u = () => h || (h = new Promise(async (f, n) => {
              await (a = m.createElement("script"));
              e.set("libraries", [...r] + ",geometry,directions,");
              for (k in g) e.set(k.replace(/[A-Z]/g, t => "_" + t[0].toLowerCase()), g[k]);
              e.set("callback", c + ".maps." + q);
              a.src = `https://maps.${c}apis.com/maps/api/js?` + e;
              d[q] = f;
              a.onerror = () => h = n(Error(p + " could not load."));
              a.nonce = m.querySelector("script[nonce]")?.nonce || "";
              m.head.append(a)
            }));
          d[l] ? console.warn(p + " only loads once. Ignoring:", g) : d[l] = (f, ...n) => r.add(f) && u().then(() => d[l](f, ...n))
        })({
          key: "AIzaSyAG03HmQeEmm_CNyhG5zT7OrIJHdmcW5nU",
          v: "weekly",
          // Use the 'v' parameter to indicate the version to use (weekly, beta, alpha, etc.).
          // Add other bootstrap parameters as needed, using camel case.
        });
      </script>
      <script>
        let map;
        var markers = [];
        let stars = null;
        let locationStatus;

        async function initMap() {
          // Try to get the user's location
          navigator.geolocation.getCurrentPosition(
            async (position) => {
                const userLocation = {
                  lat: position.coords.latitude,
                  lng: position.coords.longitude
                };

                // Call the function to initialize the map with the user's location
                await initializeMap(userLocation);
                locationStatus = true
                // Add a marker for the user's location
                const userMarker = new google.maps.Marker({
                  position: userLocation,
                  map,
                  title: "Your Location",
                  icon: {
                    url: "https://maps.google.com/mapfiles/ms/micons/blue.png", // You can use a different icon URL
                    scaledSize: new google.maps.Size(25, 25)
                  }
                });
              },
              (error) => {
                // Handle errors if geolocation is not available or denied by the user
                locationStatus = false;
                console.error("Error getting user location:", error);
                // You can choose to default to a specific location if needed
                const defaultLocation = {
                  lat: 37.362596,
                  lng: -122.072965
                };
                initializeMap(defaultLocation);
              }
          );
        }

        async function initializeMap(userLocation) {
          console.log(userLocation)
          let directionsService;
          let directionsRenderer;
          const {
            Map
          } = await google.maps.importLibrary("maps");
          const {
            AdvancedMarkerElement
          } = await google.maps.importLibrary("marker");

          const mapOptions = {
            zoom: 3,
            center: userLocation,
            minZoom: 3, // Set the minimum zoom level here
          };

          // The map, centered at the user's location
          map = new google.maps.Map(document.getElementById("map"), mapOptions);
          directionsService = new google.maps.DirectionsService();
          directionsRenderer = new google.maps.DirectionsRenderer();
          directionsRenderer.setMap(map);
          let openInfoWindow = null;
          let openOutWindow = null;

          const geocoder = new google.maps.Geocoder();
          console.log("<?= $auth_user_location ?>")
          let auth_location = "<?= $auth_user_location ?>"
          if (locationStatus === false && auth_location != "not_set") {
            geocoder.geocode({
              address: "<?= $auth_user_location ?>"
            }, (results, status) => {
              if (status === google.maps.GeocoderStatus.OK) {
                userLocation = results[0].geometry.location;
                const userMarker = new google.maps.Marker({
                  position: results[0].geometry.location,
                  map,
                  title: "Your Location",
                  icon: {
                    url: "https://maps.google.com/mapfiles/ms/micons/blue.png", // You can use a different icon URL
                    scaledSize: new google.maps.Size(25, 25)
                  }
                });
              }
            })
          }


          var addresses = [];

          allShops.forEach((shop) => {
            geocoder.geocode({
              address: shop.address + ' ' + shop.city + ' ' + shop.state + ' ' + shop.country,
            }, (results, status) => {
              if (status === google.maps.GeocoderStatus.OK) {

                const location = results[0].geometry.location;
                const MarkerIcon = document.createElement("img");
                MarkerIcon.style.width = '25px'
                MarkerIcon.style.height = '25px'
                MarkerIcon.style.borderRadius = '20px'
                MarkerIcon.src = '<?= base_url(); ?>' + shop.avatar;
                // Create an advanced marker at the geocoded location
                licensed = '<i class="icon-verified icon-verified-member" ></i>'
                verified = '<i class="icon-verified icon-verified-member" style="color:orange"></i>'
                messageBtn = '<p  data-toggle="modal" data-target="#loginModal" class=" btn" style="text-align: center;margin: 10px 0 0 0;color:#FA7348;cursor:pointer">Ask Question</p>';
                imgTag = '';
                shareBtn = '<span class=" rounded-circle share-btn " data-url="<?php echo base_url() ?>profile/' + shop.slug + '" data-name="' + shop.shop_name + '" onClick = "shareShop(this)"> <i class="fa-solid fa-share-nodes"></i></span>';
                locationBtn = '<span  onClick = "getDirection(this)" class=" rounded-circle" data-address="' + shop.address + ' ' + shop.city + ' ' + shop.state + ' ' + shop.country + '" > <i class="fa-solid fa-location-dot"></i></span>';
                phoneBtn = '<a href="tel:' + shop.phone_number + '" class=" rounded-circle share-btn " data-url="profile/" data-name=""> <i class="fa-solid fa-phone"></i></a>';

                online = ' <p class="p-last-seen" style="position:absolute; top:40px; right:40%"><span class="last-seen" style="padding:0 2px"></span><span class="last-seen"> <i class="icon-circle"></i> </span></p>'
                if (shop.isOnline) {
                  online = ' <p class="p-last-seen" style="position:absolute; top:40px; right:40%"><span class="last-seen" style="padding:0 2px"></span><span class="last-seen last-seen-online"> <i class="icon-circle"></i> </span></p>'
                }
                if (shop.rating.rating == 0) {
                  stars = '<i class="icon-star-o"></i><i class="icon-star-o"></i><i class="icon-star-o"></i><i class="icon-star-o" ></i><i class="icon-star-o"></i>'
                }
                if (shop.rating.rating >= 1) {
                  stars = '<i class="icon-star "></i><i class="icon-star-o"></i><i class="icon-star-o"></i><i class="icon-star-o" ></i><i class="icon-star-o"></i>'
                }
                if (shop.rating.rating >= 1) {
                  stars = '<i class="icon-star "></i><i class="icon-star-o"></i><i class="icon-star-o"></i><i class="icon-star-o" ></i><i class="icon-star-o"></i>'
                }
                if (shop.rating.rating >= 2) {
                  stars = '<i class="icon-star"></i><i class="icon-star"></i><i class="icon-star-o"></i><i class="icon-star-o"></i><i class="icon-star-o"></i>'
                }
                if (shop.rating.rating >= 3) {
                  stars = '<i class="icon-star"></i><i class="icon-star"></i><i class="icon-star"></i><i class="icon-star-o"></i><i class="icon-star-o"></i>'
                }
                if (shop.rating.rating >= 4) {
                  stars = '<i class="icon-star"></i><i class="icon-star"></i><i class="icon-star"></i><i class="icon-star"></i><i class="icon-star-o"></i>'
                }
                if (shop.rating.rating >= 5) {
                  stars = '<i class="icon-star"></i><i class="icon-star"></i><i class="icon-star"></i><i class="icon-star"></i><i class="icon-star"></i>'
                }

                followUnFollow = '<span  data-toggle="modal" data-target="#loginModal" class=" btn" style=""><i class="fa-solid fa-user-plus"></i></span>';
                <?php if ($this->auth_check) : ?>

                  if (<?php echo $this->auth_user->id; ?> == shop.id) {
                    followUnFollow = ''
                  } else {
                    if (shop.follows) {
                      followUnFollow = '<span  data-toggle="modal" data-target="#loginModal" class=" btn " style=" "  onClick = "follow_unfollow_submit_card(this)" id="follow_unFollow_btn"  shop ="' + shop.id + '"><i class="fa-solid fa-user-minus"></i></span></span>'
                    } else {
                      followUnFollow = '<span  data-toggle="modal" data-target="#loginModal" class=" btn " style=" "  onClick = "follow_unfollow_submit_card(this)" id="follow_unFollow_btn"  shop ="' + shop.id + '"><i class="fa-solid fa-user-plus"></i></span></span>'
                    }
                  }


                  if (<?= $this->auth_user->id ?> == shop.id) {
                    messageBtn = '<a href="<?= base_url() ?>dashboard" class=" btn" style="text-align: center;margin: 10px 0 0 0;color:#FA7348;cursor:pointer">Dashboard</a>'
                    shareBtn = '';
                    locationBtn = '';
                    phoneBtn = '';
                  } else {
                    messageBtn = '<p onclick="openCoversationContactButtons(' + shop.id + ')" class=" btn" style="text-align: center;margin: 10px 0 0 0;color:#FA7348;cursor:pointer">Ask Question</p>'
                    shareBtn = '<span class=" rounded-circle share-btn " data-url="<?php echo base_url() ?>profile/' + shop.slug + '" data-name="' + shop.shop_name + '" onClick = "shareShop(this)"> <i class="fa-solid fa-share-nodes"></i></span>';
                    locationBtn = '<span  onClick = "getDirection(this)" class=" rounded-circle" data-address="' + shop.address + ' ' + shop.city + ' ' + shop.state + ' ' + shop.country + '" > <i class="fa-solid fa-location-dot"></i></span>';
                    phoneBtn = '<a href="tel:' + shop.phone_number + '" class=" rounded-circle share-btn " data-url="profile/" data-name=""> <i class="fa-solid fa-phone"></i></a>';
                  }

                <?php endif; ?>
                if (shop.cover_image != null) {
                  imgTag = '<img src="<?= base_url() ?>' + shop.cover_image + '" style="position:absolute; width:100%;height:100%;z-index:-1;opacity: 0.7;"></img>'
                }
                var advancedMarker = new google.maps.Marker({
                  position: location,
                  map,
                  title: shop.shop_name,
                  content: '<div style="width:100%; max-width:300px; display:flex; justify-content:center;flex-wrap:wrap;padding-bottom:5px">' +
                    '<div  style="display:flex; position:relative;width:100%; max-height:60px;min-height:55px">' + imgTag +

                    '<div style="width:50px;height:50px;margin:auto; border-radius:50%;overflow:hidden">' +
                    '<img  onclick="MapQrcode(this)" alt="' + shop.shop_name + '" id="qrpopup-btn2" data-toggle="modal" data-target="#qrPopup" data-slug= "<?php echo base_url() . "profile/" ?>' + shop.slug + '" style="width:100%;" src="<?= base_url(); ?>' + shop.avatar + '">' + online +
                    '</div></div>' +
                    '<div>' +
                    '<p style="text-align: center;margin: 10px 0 0 0;font-size:16px;font-weight:600;display: flex;justify-content: center;align-items: center;">' + shop.shop_name +
                    '<i ' + shop.verify + '></i></p>' +
                    '<p style="text-align: center;margin: 10px 0 0 0;font-size:14px;">' + shop.about_me + '</p>' +
                    '<div class="map-btn">' +
                    '<div class="reviews-map" style="width:100%; display:flex; justify-content:center; padding:5px 0">' + stars + '  &nbsp;(' + shop.rating.rating + '/' + shop.rating.count + ')</div>' +
                    '<div style="display:flex; justify-content:center; width:100%" class="icon-map-card">' +

                    phoneBtn +
                    followUnFollow +
                    shareBtn +
                    locationBtn +
                    '</div>' +
                    '<a class=" btn"  style="color:#FA7348; text-align: center;margin: 10px 0 0 0;font-size:14px;" href="<?php echo base_url() . "profile/" ?>' + shop.slug + '">View Shop </a>' +
                    messageBtn +
                    '</div>' +
                    '</div></div>',
                  // icon: {url:'<?= base_url(); ?>'+address[1], scaledSize: new google.maps.Size(25, 25),}
                  shopLocatpn: location
                });
                advancedMarker.shopId = shop.id
                markers.push(advancedMarker);

                // Add a click event listener to the advanced marker to show an info window
                const infoWindow = new google.maps.InfoWindow({
                  content: advancedMarker.content
                });

                // google.maps.event.addListener(advancedMarker, "mouseover", () => {

                // });
                google.maps.event.addListener(advancedMarker, "mouseover", () => {
                  // Close the currently open info window (if any)
                  console.log("entered")
                  // 

                  if (openInfoWindow) {
                    openInfoWindow.close();
                  }
                  if (openOutWindow) {
                    openOutWindow.close();
                  }


                  // Open the clicked info window
                  infoWindow.open(map, advancedMarker);
                  openInfoWindow = infoWindow;
                  // map.setCenter(advancedMarker.getPosition());
                  // map.setZoom(15);

                });
                google.maps.event.addListener(advancedMarker, "click", () => {
                  if (document.getElementById(shop.id)) {
                    document.getElementById(shop.id).classList.add('shop-active')

                    activeElement = document.querySelectorAll('.shop-active')
                    activeElement.forEach(element => {
                      if (element.id != shop.id) {
                        // console.log(element)
                        //     if( activeElement.classList.contains('shop-active')){
                        element.classList.remove('shop-active')

                        //     }
                      }

                    });

                  }
                  if (openInfoWindow) {
                    openInfoWindow.close();
                  }
                  if (openOutWindow) {
                    openOutWindow.close();
                  }


                  // Open the clicked info window
                  infoWindow.open(map, advancedMarker);
                  openInfoWindow = infoWindow;
                  nameList = document.getElementById('shop-list')
                  nameListItem = document.getElementById(advancedMarker.shopId)
                  // console.log(nameListItem)
                  if (nameListItem) {
                    const scrollTo = nameListItem.getBoundingClientRect().top - nameList.getBoundingClientRect().top;
                    // Scroll the sidebar to the calculated position
                    // console.log(nameListItem.getBoundingClientRect())
                    // console.log(nameList.getBoundingClientRect())
                    // console.log(nameList.scrollTop)
                    nameList.scrollTo({
                      top: scrollTo,
                      behavior: "smooth"
                    });
                  }
                });
                // console.log(markers)
              } else {
                console.error("Geocode was not successful for the following reason:", status);
              }
            });
            // console.log(markers.length)
          });

          // setTimeout(() => {
          //   // console.log(markers.length)
          //   const markerCluster = new MarkerClusterer(map, markers, {
          //     imagePath: "https://developers.google.com/maps/documentation/javascript/examples/markerclusterer/m",
          //     maxZoom: 10,
          //   });
          // }, 1000);

          shop_items = document.querySelectorAll('.shops-item')
          shop_items.forEach(shop_item => {
            shop_item.addEventListener('click', (e) => {

              console.log(e.target.id)
              const marker = markers.find((m) => m.shopId == e.currentTarget.id)
              if (marker) {

                activeElement = document.querySelectorAll('.shop-active')
                activeElement.forEach(element => {
                  if (element.id != marker.shopId) {
                    console.log(element)
                    //     if( activeElement.classList.contains('shop-active')){
                    element.classList.remove('shop-active')

                    //     }
                  }
                });
                const infoWindows = new google.maps.InfoWindow({
                  content: marker.content,

                });

                if (openOutWindow) {
                  openOutWindow.close();
                }
                if (openInfoWindow) {
                  openInfoWindow.close();
                }
                map.setCenter(marker.getPosition());
                map.setZoom(15);
                // Open the clicked info window
                infoWindows.open(map, marker);
                openOutWindow = infoWindows;
              } else {
                console.log("no location")
              }



            })
          })
          //   setTimeout(() => {
          //   console.log()
          //   element = document.getElementById('map');
          //   element.addEventListener('click', (e) => {
          //     console.log(e.currentTarget)
          //     console.log(e.target)
          //     if(e.target == element){

          //       if (openOutWindow) {
          //       openOutWindow.close();
          //     }
          //     if (openInfoWindow) {
          //       openInfoWindow.close();
          //     }
          //     }

          //   })
          // }, 300);
          map.addListener('click', function(event) {
            // Check if info box is open
            if (openOutWindow) {
              openOutWindow.close();
            }
            if (openInfoWindow) {
              openInfoWindow.close();
            }
          });



        }



        // Continue with the rest of your existing code...

        // Initialize the map
        initMap();


        allShops = <?= json_encode($results); ?>;

        function follow_unfollow_submit_card(element) {

          let shop_id = element.getAttribute('shop')

          const xhrs = new XMLHttpRequest();
          xhrs.onreadystatechange = function() {
            if (xhrs.readyState === XMLHttpRequest.DONE) {

              if (xhrs.status === 200) {
                // console.log(element.innerText)
                // if (element.innerText == "Follow") {
                //   element.innerText = "Unfollow"
                //   listElement = document.querySelector(".list-shop-" + shop_id)
                //   listElement.innerHTML = '<i class="fa-solid fa-user-minus"></i>'
                // } else {
                //   element.innerText = "Follow"
                //   listElement = document.querySelector(".list-shop-" + shop_id)
                //   listElement.innerHTML = '<i class="fa-solid fa-user-plus"></i>'
                // }
                location.reload()

              }
            }
          }
          const formData = new FormData();
          formData.append(mds_config.csfr_token_name, getCookie(mds_config.csfr_cookie_name));
          formData.append('following_id', element.getAttribute('shop'));
          formData.append('follower_id', <?= $this->auth_user->id ?>);
          xhrs.open("POST", mds_config.base_url + "follow-unfollow-user-ajax");
          xhrs.send(formData);
        }

        function getDirection(element) {

          const address = element.getAttribute("data-address"); // Replace with the desired address

          const isMobile = /iPhone|iPad|iPod|Android/i.test(navigator.userAgent);
          const mapUrl = isMobile ?
            `https://maps.google.com/maps?q=${encodeURIComponent(address)}` :
            `https://www.google.com/maps/place/${encodeURIComponent(address)}`;

          window.open(mapUrl, '_blank');
        }
        async function shareShop(element) {
          const urlToShare = element.getAttribute("data-url");
          const shopName = element.getAttribute("data-name");
          try {
            // Replace with your desired URL

            if (navigator.share) {
              await navigator.share({
                title: `Share ${shopName} Profile`,
                //   text: 'Description of shared content',
                url: urlToShare
              });
              console.log('Shared successfully');
            } else {
              throw new Error('Web Share API not supported');
            }
          } catch (error) {
            console.error('Error sharing:', error);
            // Fallback for browsers that do not support Web Share API
            // You can create your custom sharing functionality here for unsupported browsers
          }
        }







        function getCookie(name) {
          // ... your existing getCookie function
        }

        function calculateAndDisplayRoute(directionsService, directionsRenderer, userLocations, shopLocation) {
          // var markerPositio = new google.maps.LatLng(shopLocation);
          console.log(shopLocation + "shop lication")
          directionsService.route({
            origin: userLocations,
            destination: shopLocation,
            travelMode: 'WALKING', // You can change the travel mode as needed (e.g., 'WALKING', 'BICYCLING')
            drivingOptions: {
              departureTime: new Date( /* now, or future date */ ),
              trafficModel: 'pessimistic'
            },
            unitSystem: google.maps.UnitSystem.IMPERIAL

          }, (response, status) => {
            if (status === 'OK') {
              console.log(response)
              directionsRenderer.setDirections(response);
            } else {
              console.error('Directions request failed due to ' + status);
            }
          });
        }
      </script>


      <div id="map" style="height:100%"></div>
      <div class="shop-list-container" id="shop-list-container">
        <div class="shop-list-header" id="collapse-button">
          <span>
            <h5>Shops</h5>
          </span>
          <span id="collapse-arrow"><i class="icon-arrow-down"> </i> <i class="icon-arrow-up"> </i></span>
        </div>
        <div id="shop-list" style="overflow-y: scroll; flex:1">
          <?php
          foreach ($results as $shop) {
          ?>
            <div class="shops-item shop-details-card " id="<?= $shop->id ?>">
              <div class="row m-0 align-items-center">
                <div class="shop-img-container" style="width: 95px;position:relative">
                  <div style="width: 90px; height:90px;" class="rounded-circle - overflow-hidden">
                    <img src="<?php echo base_url() . $shop->avatar ?>" class="img-fluid" alt="" id="qrpopup-btn2" data-toggle="modal" data-target="#qrPopup" data-slug="<?php echo base_url() . 'profile/' . $shop->slug ?>">
                   
                    
                  </div>
                  <p style="position: absolute;top:80%;right:15%"> 
                    <?php
                      if($shop->isOnline){
                        echo '<span class="last-seen last-seen-online"> <i class="icon-circle"></i> </span>';
                      }
                      else{
                        echo '<span class="last-seen"> <i class="icon-circle"></i> </span>';
                      }
                    ?></p>
                </div>
                <div style="flex:1;" class="shop-details p-2">
                  <div>
                    <p class="m-0"><a href="<?php echo base_url() . "profile/" . $shop->slug ?>"><?php echo $shop->shop_name ?></a>
                      <?php
                      if ($shop->acc_type == 3) {
                        echo ' <i class="icon-verified icon-verified-member"></i>';
                      } elseif ($shop->acc_type == 2) {
                        echo  '<i class="icon-verified icon-verified-member" style="color:orange"></i>';
                      } else {
                      }
                      ?>
                    </p>
                  </div>
                  <div class="map-rating">
                    <?php $this->load->view("partials/_review_stars", ['review' => $shop->rating->rating]); ?>
                    &nbsp;
                    <span>(<?php echo "{$shop->rating->count}" ?>)</span>
                  </div>
                  <div class="row m-0 shop-actions-buttons" id="shop-buttoms">


                    <?php if ($shop->id != $this->auth_user->id) :
                    ?>
                      <a href="tel:<?php $shop->phone_number ?>" onclick="(e)=>{e.stopPropagation()}" class=" rounded-circle  " data-url="<?php echo base_url() . "profile/" . $shop->slug ?>" data-name="<?php echo $shop->shop_name ?>"> <i class="fa-solid fa-phone"></i></a>
                      <?php if ($this->auth_check) :
                        echo ' <span  class=" rounded-circle follow_unFollow_btn list-shop-' . $shop->id . ' "   shop ="' . $shop->id . '" ><i class="fa-solid fa-comment"></i></span>';
                      else :
                        echo ' <span class=" rounded-circle    list-shop-' . $shop->id . '" data-toggle="modal" data-target="#loginModal"   shop ="' . $shop->id . '" auth-user = "' . $this->auth_user->id . '" > <i class="fa-solid fa-comment"></i></span>';
                      endif;
                      ?>


                      <span class=" rounded-circle share-btn " data-url="<?php echo base_url() . "profile/" . $shop->slug ?>" data-name="<?php echo $shop->shop_name ?>"> <i class="fa-solid fa-share-nodes"></i></span>
                      <span class=" rounded-circle  directionsButton" data-address="<?php echo $shop->address . ' ' . $shop->city . ' ' . $shop->state . ' ' . $shop->country ?>"> <i class="fa-solid fa-location-dot"></i></span>
                      <span class=" rounded-circle  "> <a href="<?php echo base_url() . "profile/" . $shop->slug ?>"><i class="fa-solid fa-arrow-up-right-from-square"></i></a></span>
                    <?php
                    endif;
                    ?>
                  </div>
                </div>
              </div>
            </div>
          <?php
          }
          # code...
          ?>






        </div>

      </div>
    </section>

  </div>
</div>