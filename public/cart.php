<?php
require_once '../resources/config.php';
if (isset($_GET['add'])) {
    $get_id = cleanData($_GET['add']);
    $query = query("SELECT * FROM products WHERE product_id = $get_id");
    confirmQuery($query);
    while ($row = fetchQuery($query)) {
        $product_name = $row['product_title'];
        $product_quantity = $row['product_quantity'];
        if ($product_quantity > ($_SESSION["product_" . $_GET['add']])) {
            $_SESSION["product_" . $_GET['add']] += 1;
            redirect('checkout.php');
        } else {
//            setMessage("We are sorry for the inconvenience, but there are only " . $product_quantity . " " .
//                $product_name . "(s) available at this moment.");
            redirect('checkout.php');
        }
    }
}


if (isset($_GET['remove'])) { // if below 0??
    if ($_SESSION["product_" . $_GET['remove']] > 0) {
        $_SESSION["product_" . $_GET['remove']]--;
        redirect('checkout.php');
        if ($_SESSION["product_" . $_GET['remove']] < 1) {
            unset($_SESSION['item_total']);
            unset($_SESSION['item_quantity']);
            redirect('checkout.php');
        }
    } else {
        redirect('checkout.php');
    }
}

if (isset($_GET['delete'])) {
    $_SESSION["product_" . $_GET['delete']] = 0;
    unset($_SESSION['item_total']);
    unset($_SESSION['item_quantity']);
    redirect('checkout.php');
}

function cart()
{
    $total = 0;
    $item_quantity = 0;
    $item_name = 1;
    $item_number = 1;
    $amount = 1;
    $quantity = 1;
    foreach ($_SESSION as $name => $value) {
        if ($value > 0) {
            if (substr($name, 0, 8) === 'product_') {
                $get_name = cleanData($name);
                $get_value = cleanData($value);
                $length = strlen($name) - 8;
                $id = substr($name, 8, $length);
                $query = query("SELECT * FROM products WHERE product_id = $id");
                confirmQuery($query);
                while ($row = fetchQuery($query)) {
                    $product_id = $row['product_id'];
                    $product_title = $row['product_title'];
                    $product_price = $row['product_price'];
                    $product_quantity = $row['product_quantity'];
                    $product_category_id = $row['product_category_id'];
                    $product_description = $row['product_description'];
                    $product_image = $row['product_image'];
                    $subtotal = $value * $product_price;
                    $item_quantity += $value;
                    $products = <<<PRODUCTS
        
                  <tr>
                    <td>{$product_title}</td>
                    <td>{$product_price}</td>
                    <td>{$product_quantity}</td>
                    <td>{$value}</td>
                    <td><span class="amount">&#36;</span>{$subtotal}</td>
                    <td class="text-center">
                        <a href="cart.php?remove={$product_id}" class="btn btn-warning btn-sm">-</a>
                        <a href="cart.php?add={$product_id}" class="btn btn-success btn-sm">+</a>
                        <a href="cart.php?delete={$product_id}" class="btn btn-danger btn-sm">X</a>
                    </td>
                  </tr>                             
                                <input type="hidden" name="item_name_{$item_name}" value="{$product_title}">
                                <input type="hidden" name="item_number_{$item_number}" value="{$product_id}">
                                <input type="hidden" name="amount_{$amount}" value="{$product_price}">
                                <input type="hidden" name="quantity_{$quantity}" value="{$value}">
 

PRODUCTS;

                    echo $products;
                }
                //product in the cart
                $_SESSION['item_total'] = $total += $subtotal;
                $_SESSION['item_quantity'] = $item_quantity;
                //paypal
                $item_name++;
                $item_number++;
                $amount++;
                $quantity++;
            }
        }
    }

}

function show_paypal()
{
    if (isset($_SESSION['item_quantity'])) {


        $paypalBtn = <<<BUTTON

       <input type="image" name="upload"
                   src="https://www.paypalobjects.com/en_US/i/btn/btn_buynow_LG.gif"
                   alt="PayPal - The safer, easier way to pay online">
BUTTON;
        echo $paypalBtn;

    } else {
        echo '<h3 class="">Your shopping cart is empty</h3>';
    }
}

