<?php
        include('connect.php');
        session_start();
        // echo '<pre>';
        // print_r($_SESSION);
        // echo '<pre>';
        // $MedId = !empty($MedId) ? 0 : $_REQUEST['MedId'];
        // $act = !empty($act) ? 0 : $_REQUEST['act'];
        // $Quantity = !empty($Quantity) ? 0 : $_REQUEST['quantity'];
    
        // if($act=='add' && !empty($MedId))
        // {
        //     if(isset($_SESSION['cart'][$MedId]))
        //     {
        //         $_SESSION['cart'][$MedId]+=(int)$Quantity;    
        //     }
        //     else
        //     {
        //         $_SESSION['cart'][$MedId]+=(int)$Quantity;  
        //     }
        // }
     
        // else if($act=='remove' && !empty($MedId))
        // {
        //     unset($_SESSION['cart'][$MedId]);
        // }
     
        /*if($act=='update')
        {
            $price_array = $_POST['MedPrice'];
            foreach($price_array as $MedId=>$MedPrice)
            {
                $_SESSION['cart'][$MedId]=$MedPrice;
            }
        }*/
        
        if (isset($_REQUEST['btn-Order'])) 
        {
            date_default_timezone_set("Asia/Bangkok");
            $OrderDate = date("Y-m-d h:i:sa");
            $OrderStatus = "Ordering";
            $OrderPrice = $_REQUEST['total'];
            $OrderTotal = ($OrderPrice * 0.07)+$OrderPrice;
            $DealerId = $_REQUEST['selDealer'];
            $StaffName = $_SESSION['StaffName'];
            
                if (empty($_SESSION['cart']))
                {
                $errorMsg = "Please Select Medicine";
                header("refresh:1;Medshow.php");
                }else 
                    if (!isset($errorMsg)) 
                    {
                        $sql = "INSERT INTO tbl_order(OrderDate, OrderStatus, OrderPrice, OrderTotal, DealerId, StaffName ) VALUES ('$OrderDate', '$OrderStatus', '$OrderPrice','$OrderTotal', '$DealerId', '$StaffName')";
                        if ($conn->query($sql) === TRUE) {} 
                        else {echo "Error updating record: " . $conn->error;}

                        foreach($_SESSION['cart'] as $MedId=>$Quantity)
                            {
                                $query = "SELECT OrderId FROM tbl_order ORDER BY OrderId DESC LIMIT 1";
                                $result = mysqli_query($conn, $query); 
                                $row = mysqli_fetch_array($result);
                                $OrderId = $row["OrderId"];

                                $sql ="SELECT * FROM tbl_med WHERE $MedId = MedId";
                                $result = $conn->query($sql);
                                $data = array();
                                while($row = $result->fetch_assoc()) 
                                {
                                    $data[] = $row;       
                                }
                                foreach($data as $key => $Med)
                                {
                                    $Medsum = $Quantity*$Med["MedPrice"];
                                    $sql = "INSERT INTO tbl_orderdetail(OrderId, MedId, Qty, Price) VALUES ('$OrderId', '$MedId', '$Quantity','$Medsum')";
                                    if ($conn->query($sql) === TRUE) {unset($_SESSION['cart']);} 
                                    else {echo "Error updating record: " . $conn->error;}
                                }
                            }
                            header("refresh:1;main.php");
                    }
        }
?>
     
     <!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>

<body>
        <?php
            include('slidebar.php');
        ?>

<nav class="navbar navbar-expand-sm navbar-dark bg-dark">
            <div class="container">
            <h1 class="navbar-brand">Cart</h1>
                <button class="navbar-toggler" data-bs-toggle="collapse" data-bs-target="#navbar1">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div id="navbar1" class="collapse navbar-collapse">
                    <ul class="navbar-nav ms-auto">

                        <li class="nav-item">
                            <a class="nav-link"><?php echo $_SESSION['StaffName'] ?></a>                
                        </li>  
                        
                        <li class="nav-item">
                            <td><a href="index.php?logout='1'" class ="btn btn-warning">Logout</a></td>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>
    
    <?php 
         if (isset($errorMsg)) {
    ?>
        <div class="alert alert-danger">
            <strong>Wrong! <?php echo $errorMsg; ?></strong>
        </div>
    <?php } ?>
    
    <?php 
         if (isset($updateMsg)) {
    ?>
        <div class="alert alert-success">
            <strong>Success! <?php echo $updateMsg; ?></strong>
        </div>
    <?php } ?>

  
    <form name="frmcart" method="post" >
      <table width="600" border="0" align="center" class="square">
        <tr>
          <td colspan="5" bgcolor="#CCCCCC">
          <b>Cart</span></td>
        </tr>
        <tr>
          <td bgcolor="#EAEAEA">Order</td>
          <td align="center" bgcolor="#EAEAEA">Price</td>
          <td align="center" bgcolor="#EAEAEA">Quantity</td>
          <td align="center" bgcolor="#EAEAEA">Total Price</td>
          <td align="center" bgcolor="#EAEAEA">Remove</td>
        </tr>
    <?php
    $total=0;
    if(!empty($_SESSION['cart']))
    {
        foreach($_SESSION['cart'] as $MedId=>$Quantity)
        {
            $sql = "SELECT* FROM tbl_Med WHERE MedId=$MedId";
		    $result = $conn->query($sql);
            $data = array();
            while($row = $result->fetch_assoc()) {
            $data[] = $row;  
            }
            foreach($data as $key => $Med){
		    $sum = $Med['MedPrice'] * $Quantity;
		    $total += $sum;
            echo "<tr>";
            echo "<td width='334'>" . $Med["MedName"] . "</td>";
            echo "<td width='46' align='right'>" .number_format($Med["MedPrice"],2) . "</td>";
            echo "<td width='57' align='right'>";  
            echo "<input type='text' name= $Med[MedId]; value='$Quantity' disabled size='2'/></td>";
            echo "<td width='93' align='right'>".number_format($sum,2)."</td>";
            
            echo "<td width='46' align='center'><a href='Order.php?MedId=$MedId&act=remove&quantity=0'>Remove</a></td>";
            echo "</tr>";
            }
            echo "<tr>";
          //echo "<td colspan='3' bgcolor='#CEE7FF' align='center'><b>?????????????????????</b></td>";
          
            //echo "<td align='left' bgcolor='#CEE7FF'></td>";
       
    }}
            echo "<td align = 'right'>Total Price <input type = 'text' name ='total' disabled value = '$total'></td>";
            //echo "<td align='right' bgcolor='#CEE7FF'>"."<b>".number_format($total,2)."</b>"."</td>";
            echo "</tr>";
    ?>
    <tr>
    <td><a href="Medshow.php">Medicine</a></td>
    </tr>
    </table>
                    <div class="container">
                        <label class="col-sm-3 control-label">Dealer</label>
                            <select name="selDealer">       
                                <?php 
                                        
                                        $sql = 'SELECT * FROM tbl_dealer';
                                        $result = $conn->query($sql);
                                        $data = array();
                                        while($row = $result->fetch_assoc()) {
                                            $data[] = $row;        
                                        }
                                        foreach($data as $key => $dealer){                  
                                    ?>
                                            <option value ="<?php echo $dealer["DealerId"];?>"><?php echo $dealer["DealerName"];?></option>
                                <?php } ?>      
                            </select>

                            <div class="col-sm-9">
                                <input type="submit" name = "btn-Order"class = "btn btn-info" value = "Order">
                            </div>
                        
                    </div>           
    </form>
    </body>
    </html>