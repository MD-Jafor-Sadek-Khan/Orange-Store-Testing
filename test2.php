<?php include('layouts/header.php'); ?>


<?php




include('server/connection.php');






//use the search section
if(isset($_POST['search'])){


    //1. determine page no
    if(isset($_GET['page_no']) && $_GET['page_no'] != ""){
      //if user has already entered page then page number is the one that they selected
       $page_no = $_GET['page_no'];
    }else{
      //if user just entered the page then default page is 1
      $page_no = 1;
    }






    $category = $_POST['category'];
    $price = $_POST['price'];


     //2. return number of products
     $stmt1 = $conn->prepare("SELECT COUNT(*) As total_records FROM products WHERE product_category=? AND product_price<=?");
     $stmt1->bind_param('si',$category,$price);
     $stmt1->execute();
     $stmt1->bind_result($total_records);
     $stmt1->store_result();
     $stmt1->fetch();
 
 


    //3. products per page
    $total_records_per_page = 8;


    $offset = ($page_no-1) * $total_records_per_page;


    $previous_page = $page_no - 1;
    $next_page = $page_no + 1;


    $adjacents = "2";


    $total_no_of_pages = ceil($total_records/$total_records_per_page);




     //4. get all products


     $stmt2 = $conn->prepare("SELECT * FROM products WHERE product_category=? AND product_price<=? LIMIT $offset,$total_records_per_page");
     $stmt2->bind_param("si",$category,$price);
     $stmt2->execute();
     $products = $stmt2->get_result();//[]
 








  //return all products  
}else{




    //1. determine page no
    if(isset($_GET['page_no']) && $_GET['page_no'] != ""){
      //if user has already entered page then page number is the one that they selected
       $page_no = $_GET['page_no'];
    }else{
      //if user just entered the page then default page is 1
      $page_no = 1;
    }






    //2. return number of products
    $stmt1 = $conn->prepare("SELECT COUNT(*) As total_records FROM products");
    $stmt1->execute();
    $stmt1->bind_result($total_records);
    $stmt1->store_result();
    $stmt1->fetch();




    //3. products per page
    $total_records_per_page = 8;


    $offset = ($page_no-1) * $total_records_per_page;


    $previous_page = $page_no - 1;
    $next_page = $page_no + 1;


    $adjacents = "2";


    $total_no_of_pages = ceil($total_records/$total_records_per_page);






    //4. get all products


    $stmt2 = $conn->prepare("SELECT * FROM products LIMIT $offset,$total_records_per_page");
    $stmt2->execute();
    $products = $stmt2->get_result();






}






?>










 


<div class="d-flex flex-lg-row flex-md-column">
  <!--Search-->
  <section id="search" class=" my-5 py-5 ms-2 bg-black text-white shop-search-body d-none d-lg-block">
    <div class="container py-5">
      <p class="fs-4-5">Search Products</p>
      <hr>
    </div>


        <form action="shop.php" method="POST">
         <div class="row mx-auto container">
           <div class="col-lg-12 col-md-12 col-sm-12">
           


            <p>Category</p>
               <div class="d-flex flex-column justify-content-center align-items-baseline">
               <div class="form-check">
                <input class="form-check-input" value="shoes" type="radio" name="category" id="category_one" <?php if(isset($category) && $category=='shoes'){echo 'checked';}?> >
                <label class="form-check-label" for="flexRadioDefault1">
                  Shoes
                </label>
              </div>


              <div class="form-check">
                <input class="form-check-input" value="coats" type="radio" name="category" id="category_two" <?php if(isset($category) && $category=='coats'){echo 'checked';}?>>
                <label class="form-check-label" for="flexRadioDefault2">
                  Coats
                </label>
              </div>


               <div class="form-check">
                <input class="form-check-input" value="watches" type="radio" name="category" id="category_two" <?php if(isset($category) && $category=='watches'){echo 'checked';}?>>
                <label class="form-check-label" for="flexRadioDefault2">
                  Watches
                </label>
              </div>


               <div class="form-check">
                <input class="form-check-input" value="bags" type="radio" name="category" id="category_two" <?php if(isset($category) && $category=='bags'){echo 'checked';}?>>
                <label class="form-check-label" for="flexRadioDefault2">
                  Bags
                </label>
              </div>


           </div>
         </div>


<!-- slider  -->
       
      <div class="row mx-auto container mt-5">
        <div class="col-lg-12 col-md-12 col-sm-12">


         
          <div class="d-flex justify-content-between align-items-baseline"><p>Price:</p>
            <!-- <span id="sliderValue"><?php echo (isset($price)) ? $price : "100"; ?></span> -->
            <input type="number" class="form-control ms-3 w-75 d-inline" id="priceInput" name="price" value="<?php echo (isset($price)) ? $price : "100"; ?>" min="1" max="10000">
</div>
          <input type="range" class="form-range w-100" name="price" value="<?php echo (isset($price)) ? $price : "100"; ?>" min="1" max="10000" id="customRange2">
          <div class="w-100 d-flex flex-lg-row flex-md-column justify-content-between align-items-baseline">
            <span class="d-lg-inline d-none">$1</span>
            <span class="d-lg-inline d-none">$10000</span>
          </div>
        </div>
      </div>


      <div class="form-group my-3 mx-3">
        <input type="submit" name="search" value="Search" class="btn shop-buy-btn align-self-center">
      </div>


    </form>


  </section>


  <!--Shop-->
  <section id="shop" class="mt-1 py-5">
    <div class="container mt-5 py-5">
      <h3>Our Products</h3>
      <hr>
      <p>Here you can check out our products</p>
    </div>
    <div class="row mx-auto container">




     <?php  while($row = $products->fetch_assoc()) { ?>


      <div onclick="window.location.href='single_product.html';" class="product text-center col-lg-3 col-md-4 col-sm-12">
      <a href="<?php echo "single_product.php?product_id=".$row['product_id'];?>"><img class="img-fluid mb-3" src="assets/imgs/<?php echo $row['product_image']; ?>"/></a>
       
        <div class="star">
          <i class="fas fa-star"></i>
          <i class="fas fa-star"></i>
          <i class="fas fa-star"></i>
          <i class="fas fa-star"></i>
          <i class="fas fa-star"></i>
        </div>
        <h5 class="p-name"><?php echo $row['product_name']; ?></h5>
        <h4 class="p-price">$<?php echo $row['product_price'];?></h4>
        <a  class="btn shop-buy-btn" href="<?php echo "single_product.php?product_id=".$row['product_id'];?>">Buy Now</a>
      </div>
     


      <?php } ?>




      <nav aria-label="Page navigation example" class="mx-auto">
        <ul class="pagination mt-5 mx-auto">
         
          <li class="page-item <?php if($page_no<=1){echo 'disabled';}?> ">
               <a class="page-link coral-text" href="<?php if($page_no <= 1){echo '#';}else{ echo "?page_no=".($page_no-1);} ?>">Previous</a>
          </li>




          <li class="page-item"><a class="page-link coral-text" href="?page_no=1">1</a></li>
          <li class="page-item coral-text"><a class="page-link coral-text" href="?page_no=2">2</a></li>


          <?php if( $page_no >=3) {?>
            <li class="page-item "><a class="page-link coral-text" href="#">...</a></li>
            <li class="page-item coral-text"><a class="page-link coral-text" href="<?php echo "?page_no=".$page_no;?>"><?php echo $page_no;?></a></li>
          <?php } ?>






          <li class="page-item <?php if($page_no >=  $total_no_of_pages){echo 'disabled';}?>">
                 <a class="page-link coral-text" href="<?php if($page_no >= $total_no_of_pages ){echo '#';} else{ echo "?page_no=".($page_no+1);}?>">Next</a></li>
         </ul>
      </nav>




    </div>
  </section>


</div>
 


<script>
  const slider = document.getElementById('customRange2');
  const sliderValueElement = document.getElementById('sliderValue');
  const priceInput = document.getElementById('priceInput');


  // Update the slider value element and the input value with the initial value on page load
  // sliderValueElement.textContent = slider.value;
  priceInput.value = slider.value;


  // Update the slider value element and the input value whenever the slider value changes
  slider.addEventListener('input', function() {
    const value = this.value;
    // sliderValueElement.textContent = value;
    priceInput.value = value;
  });


  // Update the slider value element and the slider value whenever the input value changes
  priceInput.addEventListener('input', function() {
    const value = this.value;
    // sliderValueElement.textContent = value;
    slider.value = value;
  });
</script>








  <?php include('layouts/footer.php'); ?>



Sample Code 2: Cart Management (HTML and PHP)

<?php include('layouts/header.php'); ?>




<?php










if(isset($_POST['add_to_cart'])){


    //if user has already added a product to cart
    if(isset($_SESSION['cart'])){


       $products_array_ids = array_column($_SESSION['cart'],"product_id"); // [2,3,4,10,15]
       //if product has already been addedcto cart or not
       if( !in_array($_POST['product_id'], $products_array_ids) ){


            $product_id = $_POST['product_id'];
     
              $product_array = array(
                              'product_id' => $_POST['product_id'],
                              'product_name' =>  $_POST['product_name'],
                              'product_price' => $_POST['product_price'],
                              'product_image' => $_POST['product_image'],
                              'product_quantity' => $_POST['product_quantity']
              );
     
              $_SESSION['cart'][$product_id] = $product_array;




        //product has already been added
       }else{
         
            echo '<script>alert("Product was already to cart");</script>';
           


       }




      //if this is the first product
    }else{
 
       $product_id = $_POST['product_id'];
       $product_name = $_POST['product_name'];
       $product_price = $_POST['product_price'];
       $product_image = $_POST['product_image'];
       $product_quantity = $_POST['product_quantity'];


       $product_array = array(
                        'product_id' => $product_id,
                        'product_name' => $product_name,
                        'product_price' => $product_price,
                        'product_image' => $product_image,
                        'product_quantity' => $product_quantity
       );


       $_SESSION['cart'][$product_id] = $product_array;
       // [ 2=>[] , 3=>[], 5=>[]  ]




    }




    //calculate total
    calculateTotalCart();




//remove product from cart
}else if(isset($_POST['remove_product'])){


  $product_id = $_POST['product_id'];
  unset($_SESSION['cart'][$product_id]);


  //calculate total
  calculateTotalCart();






}else if( isset($_POST['edit_quantity']) ){


    //we get id and quantity from the form
   $product_id = $_POST['product_id'];
   $product_quantity = $_POST['product_quantity'];


   //get the product array from the session
   $product_array = $_SESSION['cart'][$product_id];


   //update product quantity
   $product_array['product_quantity'] = $product_quantity;




   //return array back its place
   $_SESSION['cart'][$product_id] = $product_array;




   //calculate total
   calculateTotalCart();


   


}else{
  // header('location: index.php');
}










function calculateTotalCart(){


     $total_price = 0;
     $total_quantity = 0;


    foreach($_SESSION['cart'] as $key => $value){
 
        $product =  $_SESSION['cart'][$key];


        $price =  $product['product_price'];
        $quantity = $product['product_quantity'];


        $total_price =  $total_price + ($price * $quantity);
        $total_quantity = $total_quantity + $quantity;
       


    }


    $_SESSION['total'] = $total_price;
    $_SESSION['quantity'] = $total_quantity;


}


?>


    <!--Cart-->
    <section class="cart container my-5 py-5">
        <div class="container mt-5">
            <h2 class="font-weight-bolde">Your Cart</h2>
            <hr>
        </div>


        <table class="mt-5 pt-5">
            <tr>
                <th>Product</th>
                <th>Quantity</th>
                <th>Subtotal</th>
            </tr>


          <?php if(isset($_SESSION['cart'])){ ?>  


            <?php foreach($_SESSION['cart'] as $key => $value){ ?>


            <tr>
                <td>
                    <div class="product-info">
                        <img src="assets/imgs/<?php echo $value['product_image']; ?>"/>
                        <div>
                            <p><?php echo $value['product_name']; ?></p>
                            <small><span>$</span><?php echo $value['product_price']; ?></small>
                            <br>
                            <form method="POST" action="cart.php">
                                 <input type="hidden" name="product_id" value="<?php echo $value['product_id']; ?>"/>
                                 <input type="submit" name="remove_product" class="remove-btn" value="remove"/>
                            </form>
                           
                        </div>
                    </div>
                </td>


                <td>
                   
                    <form method="POST" action="cart.php">
                        <input type="hidden" name="product_id" value="<?php echo $value['product_id'];?>"/>
                        <input type="number" name="product_quantity" value="<?php echo $value['product_quantity']; ?>"/>
                        <input type="submit" class="edit-btn" value="edit" name="edit_quantity"/>
                    </form>
                   
                </td>


                <td>
                   
                    <span class="product-price">$<?php  echo $value['product_quantity'] * $value['product_price']; ?></span>
                </td>
            </tr>


         
            <?php } ?>




            <?php } ?>


         
        </table>




        <div class="cart-total">
          <table class="cart-table2">
            <tr class="cart-table2-tr">
              <td class="cart-table2-td">Total</td>
              <?php if(isset($_SESSION['cart'])){?>
                 <td>$<?php echo $_SESSION['total']; ?></td>
               <?php } ?>  
            </tr>
          </table>
        </div>
   


        <div class="checkout-container">
          <form method="POST" action="checkout.php">
             <input type="submit" class="btn checkout-btn" value="Checkout" name="checkout">
          </form>
        </div>




    </section>


    <?php include('layouts/footer.php'); ?>
