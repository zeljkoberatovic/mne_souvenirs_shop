//cart.Blade.

    //pomocu koje mijenjamo vrijednost u polju Quantity
    function updateQuantity(qty)
        {
            $('#rowId').val($(qty).data('rowid'));
            $('#quantity').val($(qty).val());
            $('#updateCartQty').submit();
            console.log(qty);
        }
    // funkcije za brisanje kartice
    function removeItemFromCart(rowId)
        {
            $('#rowId_D').val(rowId);
            $('#deleteFromCart').submit();
        }
    function clearCart()
        {
            $('#clearCart').submit();
        }

//shop.blade

        //
        $("#pagesize").on("change", function(){
            $("#size").val($("#pagesize option:selected").val());
            $("#frmFliter").submit();
         });
 
         $('#orderby').on("change", function(){
             $("#order").val($("#orderby option:selected").val());
            $("#frmFliter").submit();
         });
 
         // za filtriranje tabele brand
         function filterProductsByBrand(brand){
             var brands = "";
             $("input[name='brands']: checked").each(function(){
                 if(brands == ""){
                     brands += this.value;
                 }
                 else{
                     brands += "," + this.value;
                 }
             });
             $("#brands").val(brands);
             $("#frmFliter").submit();
         }
 
         //za filtriranje tabele category
         function filterProductsByCategory(brand){
             var categories = "";
             $("input[name='categories']: checked").each(function(){
                 if(categories == ""){
                     categories += this.value;
                 }
                 else{
                     categories += "," + this.value;
                 }
             });
             $("#categories").val(categories);
             $("#frmFliter").submit();
         }
 
         //funkcije za  koriscenje wishliste
         function addProductToWishlist(id, name, quantity, price)
         {
               $.ajax({
                 type: 'POST',
                 url: "{{ route('wishlist.store') }}",
                 data: {
                     "_token": "{{ csrf_token() }}",
                     id: id,
                     name: name,
                     quantity: quantity,
                     price: price
                 },
                 success: function (data) {
                     if (data.status == 200) {
                         getCartWishlistCount();
                         $.notify({
                             icon: "fa fa-check",
                             title: "Success!",
                             message: "Item successfully added to your wishlist!"
                           });
                         }
                     }
                 });
            }
 
            function getCartWishlistCount()
            {
             $.ajax({
                 type:"GET",
                 url:"{{route('shop.cart.wishlist.count')}}",
                 success:function(data){
                     if(data.status == 200)
                     {
                         $("#cart-count").html(data.cartCount);
                         $("#wishlist-count").html(data.wishlistCount);
                     }
                 }
             });
         }
 
         function addToWishlist() {
             let i, products = document.querySelectorAll('.wishlist');
             for(i = 0; i < products.length; i++) {
                 // console.log(products[i]);
                 products[i].addEventListener("click", function() {   
                     // console.log(this);
                     let data = this.dataset;
                     //this.classList.add('active');
                     addProductToWishlist(data.id, data.name, data.quantity, data.price);
                 
                 });
             }
         }
         addToWishlist();

        
