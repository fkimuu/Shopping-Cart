<?php

session_start();
$product_id=array();
//session_destroy();

if(isset($_POST['add_to_cart'])){
  $addCart=$_POST['add_to_cart'];
  if(isset($_SESSION['shopping_cart'])){
    //keep track of the number of products in the shopping cart
    $count= count($_SESSION['shopping_cart']);
    //create sequantial array for matching array keys to product ids
    $product_id=array_column($_SESSION['shopping_cart'],'id');

    //pre_r($product_id);
    if(!in_array(filter_input(INPUT_GET,'id'),$product_id)){
      $_SESSION['shopping_cart'][$count]=array
        (
         'id'=>filter_input(INPUT_GET,'id'),
         'name'=>filter_input(INPUT_POST,'name'),
         'price'=>filter_input(INPUT_POST,'price'),
         'quantity'=>filter_input(INPUT_POST,'quantity')
        );
    }else{//product already exists
          //match array key to the product being added to the cart
      for ($i=0; $i <count($product_id) ; $i++) {
        if ($product_id[$i]==filter_input(INPUT_GET,'id')) {
          //add item quantity to the existing product in the array
           $_SESSION['shopping_cart'][$i]['quantity']+=filter_input(INPUT_POST,'quantity');
        }
      }
    }

  }else{//if shopping cart doesn't exist ,create 1st product with array key 0
    //create array using submitted form data
    $_SESSION['shopping_cart'][0]=array
      (
       'id'=>filter_input(INPUT_GET,'id'),
       'name'=>filter_input(INPUT_POST,'name'),
       'price'=>filter_input(INPUT_POST,'price'),
       'quantity'=>filter_input(INPUT_POST,'quantity')
      );
  }
}
//pre_r($_SESSION);
if(filter_input(INPUT_GET,'action')=='delete'){
  //loop through all the products in the shopping cart till it matches with GET id variable
  foreach ($_SESSION['shopping_cart'] as $key => $product) {
    if($product['id']== filter_input(INPUT_GET,'id')){
      //remove product from shopping cart
      unset($_SESSION['shopping_cart'][$key]);
  }
}
$_SESSION['shopping_cart'] =array_values($_SESSION['shopping_cart']);
}

function pre_r($array){
  echo '<pre>';
  print_r($array);
  echo '</pre>';
}

 ?>





<html lang="en" dir="ltr">
  <head>
    <title>Shopping Cart</title>
    <link rel="stylesheet" type="text/css" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" />
    <link rel="stylesheet" type="text/css" href="css/style.css" />

  </head>
  <body>
     <div class="container">

        <?php

          $conn= mysqli_connect('localhost','root','','cart'); //initialize database connection
          //check if connection is successful
          if(!$conn){
            die('Database connection failed:' .mysqli_connect_error());
          }else{
          //  echo 'Database connection successful';
          }
          //execute a select sql Statement
          $sql= 'SELECT * FROM products';
          $result=mysqli_query($conn,$sql);

          if(!$result){
            die("Database access failed : " . mysqli_error());
          }else{
            $row=mysqli_num_rows($result);

            if($row){
              while ($rows= mysqli_fetch_array($result)){
                $product=$rows;
            ?>

            <div class="col-sm-4 col-md-3">
              <form action="cart.php?action=add&id=<?php echo $product['id']; ?>" method="post">
                  <div class="products">
                      <img src="<?php echo $product['image']; ?>" class="img-responsive" />
                      <h4 class="text-info"><?php echo $product['name']; ?></h4>
                      <h4> $<?php echo $product['price']; ?></h4>
                      <input type="text" name="quantity" value="1" class="form-control" />
                      <input type="hidden" name="name" value="<?php echo $product['name']; ?>">
                      <input type="hidden" name="price" value="<?php echo $product['price']; ?>">
                      <input type="submit" name="add_to_cart" style="margin-top:5px" value="Add to Cart" class="btn btn-info" />
                  </div>
              </form>
            </div>

            <?php
          }
        }

          }


        ?>
       <div style="clear:both"></div>
       <br />
       <div class="table-responsive">
         <table class="table">
           <tr><th colspan="5"><h3>Order Details</h3></th></tr>
           <tr>
             <th width="40%">Product Name</th>
             <th width="10%">Quantity</th>
             <th width="20%">Price</th>
             <th width="15%">Total</th>
             <th width="5%">Action</th>
           </tr>
           <?php
            if(!empty($_SESSION['shopping_cart'])){
              $total=0;
              foreach ($_SESSION['shopping_cart'] as $key => $product) {

                ?>
                <tr>
                  <td><?php echo $product['name']; ?></td>
                  <td><?php echo $product['quantity']; ?></td>
                  <td><?php echo $product['price']; ?></td>
                  <td><?php echo number_format($product['quantity']* $product['price'],2); ?></td>
                  <td>
                    <a href="cart.php?action=delete&id=<?php echo $product['id']; ?>">
                      <div class="btn-danger">Remove</div>
                    </a>
                  </td>
                </tr>

                <?php
                $total=$total + ($product['quantity'] * $product['price']);
              }
              ?>
              <tr>
                <td colspan="3" align="right">Total</td>
                <td align="right">$ <?php echo number_format($total,2); ?></td>
                <td></td>
              </tr>
              <tr>

                <td colspan="5">
                  <?php
                   if(isset($_SESSION['shopping_cart'])){
                     if(count($_SESSION['shopping_cart'])>0){
                   ?>
                   <a href="#" class="button">Checkout</a>
                   <?php
                     }
                   }
                   ?>
                </td>
              </tr>
              <?php
            }

            ?>
         </table>

       </div>

    </div>
  </body>
</html>
