require(["jquery"],function($) {
    $(document).ready(function() {
        $('#productsRangeForm').submit(function(event) {
            event.preventDefault();
            if( $(this).valid()){
                var customurl = "<?php echo $this->getUrl().'productrangetab/customer/range/'?>";
                $.ajax({
                    url: customurl,
                    type: 'POST',
                    dataType: 'json',
                    data: {
                        lowRange: $( "#lowRange" ).val(),
                        highRange: $( "#highRange" ).val(),
                        sortByPrice: $( "#sortByPrice" ).find(":selected").val(),
                    },
                    complete: function(response) {             
                        productList = response.responseJSON; 
                        $('#productList .product-items').empty();//delete previous products
                        productList.forEach(function(product, index, arr){
                            html='<div class="product-item-info" id="product-item-info_334" data-container="product-grid"><span class="product-image-container product-image-container-334" style="width: 240px;"><span class="product-image-wrapper" style="padding-bottom: 125%;">';
                            html=html+'<img class="product-image-photo" src="';
                            html=html+product.image+'" loading="lazy" width="240" height="300" alt="Lando Gym Jacket"></span>';
                            html=html+'</span><style>.product-item-name{font-size: 1.4rem;line-height: normal;letter-spacing: normal;margin-bottom: 16px;}.product-image-container-334 {width: 240px;}.product-image-container-334 span.product-image-wrapper {  padding-bottom: 125%; }</style>    </a>';
                            html=html+'<div class="product details product-item-details"><strong class="product name product-item-name"><a class="product-item-link" href="';
                            html=html+product.url+'">';
                            html=html+product.name+'</a></strong><div class="price-box price-final_price" data-role="priceBox" data-product-id="'+product.entity_id+'" data-price-box="product-id-334"><span class="normal-price">';
                            html=html+'<span class="price-container price-final_price tax weee"><span id="product-price-334" data-price-amount="99" data-price-type="finalPrice" class="price-wrapper "><span class="price">';
                            html=html+'$'+product.final_price+'</span></span></span></span></div></div></div>'
                            $('#productList .product-items').append(html);
                            console.log(html);  
                        });
                        
                    },
                    error: function (xhr, status, errorThrown) {
                        console.log('Error happens. Try again.');
                    }
                });
            }
        });
    });
});