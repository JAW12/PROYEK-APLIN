<?php
    include "system/load.php";
    /** @var PDO $db Prepared Statement */

    cekLogin($db, "", $login);

    if (isset($login)) {
        $jenisUser = "admin";
        $rowIdUserAktif = -1;
        if ($login['role'] == 1) {
            $jenisUser = "customer";
        }
        
        if ($jenisUser == "customer") {
            $dataCustomer = getCustomerData($db, $login['username']);
            $rowIdUserAktif = $dataCustomer['ROW_ID_CUSTOMER'];
        }
    }
    
    function getStatusPembayaran($status){
        $status_str = "";
        if ($status == 0) {
            $status_str = "Pending";
        }
        else if ($status == 1) {
            $status_str = "Accepted";
        }
        else if ($status == 2) {
            $status_str = "Rejected";
        }
        return $status_str;
    }

    function getCountData($data, $idxData, $valueData){
        $ctr = 0;
        foreach ($data as $key => $value) {
            if ($value[$idxData] == $valueData) {
                $ctr++;
            }
        }
        return $ctr;
    }

    function getTotalTransaction($dataHTrans){
        $total = 0;
        foreach ($dataHTrans as $key => $value) {
            if ($value['STATUS_PEMBAYARAN'] == 1) {
                $total += $value['TOTAL_TRANS'];
            }
        }
        return $total;
    }

    function showTransactionList($db, $jenisUser, $row_id_cust){
        $query = "SELECT * FROM HTRANS";
        $condition = "";
        $order = " ORDER BY STATUS_PEMBAYARAN ASC, NO_NOTA ASC";
        if ($jenisUser == "customer") {
            $condition = " WHERE ROW_ID_CUSTOMER = '{$row_id_cust}'";
        }
        $query = $query . $condition . $order;

        $dataHTrans = getQueryResultRowArrays($db, $query);

        ?>
            <div class="h1 text-center">Transaction List</div>
            <div class="h3 text-right text-success my-3">
                <?php
                    $pesan = "Total income : ";
                    if ($jenisUser == "customer") {
                        $pesan = "You have spent : ";
                    }
                    echo $pesan . getSeparatorNumberFormatted(getTotalTransaction($dataHTrans));
                ?>
            </div>
            <div class="bg-transparent h5 text-left my-3">
                <p class="text-secondary"> <?= count($dataHTrans) ?> transactions found</p>
                <?php
                    $text = "admin's";
                    if ($jenisUser == "admin") {
                        $text = "your";
                    }
                    ?>
                        <p class="text-warning"> 
                            <?= getCountData($dataHTrans, "STATUS_PEMBAYARAN", 0) ?> pending transactions awaiting <?= $text ?> approval
                        </p>
                    <?php   
                ?>
            </div>          
            <table class="table table-hover table-striped table-bordered">
                <thead class="thead-dark text-center">
                    <tr>
                        <th scope="col">#</th>
                        <th scope="col">Row ID</th>
                        <th scope="col">Date</th>
                        <th scope="col">Invoice Number</th>                        
                        <th scope="col">Customer</th>                        
                        <th scope="col">Total</th>       
                        <th scope="col">Status</th>
                        <th scope="col">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                        if (count($dataHTrans) <= 0) {
                            ?>
                                <tr>
                                    <th colspan="8" class="text-center">You have not done any transaction</th>
                                </tr>
                            <?php
                        }
                        else{
                            $ctrNum = 0;
                            foreach ($dataHTrans as $key => $value) {
                                $ctrNum++;
                                $row_id_cust = $value['ROW_ID_CUSTOMER'];
                                $query="SELECT * FROM CUSTOMER WHERE ROW_ID_CUSTOMER = '{$row_id_cust}'";
                                $dataCustomer = getQueryResultRow($db, $query);
                                $namaCustomer = $dataCustomer['NAMA_DEPAN_CUSTOMER'] . " " . $dataCustomer['NAMA_BELAKANG_CUSTOMER'];
                                $namaCustomer = ucwords(strtolower($namaCustomer));

                                $status = $value['STATUS_PEMBAYARAN'];
                                $status_str = getStatusPembayaran($status);

                                $cl = "";
                                if ($status_str == "Pending") {
                                    $cl = "bg-warning text-dark";
                                }
                                else if ($status_str == "Accepted") {
                                    $cl = "bg-success text-light";
                                }
                                else if ($status_str == "Rejected") {
                                    $cl = "bg-danger text-light";
                                }                                
                                
                                ?>
                                <form method="POST">
                                    <tr>
                                        <input type="hidden" name="row_id_htrans" value='<?= $value['ROW_ID_HTRANS'] ?>'>
                                        <th scope="row" class="align-middle"><?= $ctrNum ?></th>
                                        <th class="align-middle"><?= $value['ROW_ID_HTRANS'] ?></th>
                                        <td class="align-middle"> <?= getDateFormatted($value['TANGGAL_TRANS']) ?> </td>
                                        <td class="align-middle"> <?= $value['NO_NOTA'] ?> </td>
                                        <td class="align-middle"> <?= $namaCustomer ?> </td>
                                        <td class="text-right align-middle"> <?= getSeparatorNumberFormatted($value['TOTAL_TRANS']) ?> </td>
                                        <td class="<?= $cl ?> text-center align-middle"> <?= $status_str ?> </td>
                                        <?php    
                                            if ($status_str == "Pending" && $jenisUser != "customer") {
                                                ?>
                                                    <td class="d-flex flex-wrap justify-content-around">
                                                        <button type="submit" class="btn btn-success rounded ml-2" name="acceptOrder">
                                                            <i class="fas fa-check" aria-hidden="true"></i>
                                                        </button>
                                                        <button type="submit" class="btn btn-danger rounded" name="rejectOrder">
                                                            <i class="fas fa-times" aria-hidden="true"></i>
                                                        </button>
                                                        <br/>          
                                                    </td>    
                                                <?php
                                            }                                                
                                        ?>    
                                        <?php
                                            $colspan = 2;
                                            if ($status_str == "Pending") {
                                                $colspan = "";
                                            }
                                            ?>
                                                <td colspan="<?= $colspan ?>" class="d-flex flex-wrap justify-content-around align-middle">
                                                    <button type="submit" class="btn btn-info rounded" name="viewDetail" formaction="transaction-detail.php">
                                                            View Detail
                                                    </button>   
                                                </td>  
                                            <?php
                                        ?>
                                                               
                                    </tr>
                                </form>
                                   
                                <?php
                            }
                        }
                    ?>        
                    <!-- <tr class="bg-warning text-dark font-weight-bold text-center">
                        <td colspan="8" class="align-middle">
                            <?php
                                // echo "Last Updated: ".date("F d Y H:i:s.", 
                                // filemtime("transaction-list.php"));
                                // echo "~";
                            ?>
                        </td>
                    </tr>         -->
                    <tr class="bg-dark text-warning font-weight-bold text-center">
                        <td colspan="8" class="align-middle">
                            &nbsp;
                        </td>
                    </tr>           
                </tbody>
            </table>
        <?php   
    }

    function updateTransactionStatus($db, $row_id_htrans, $newStatus){
        /** @var PDO $db Prepared Statement */
        $query = "UPDATE HTRANS SET STATUS_PEMBAYARAN = :new_status WHERE ROW_ID_HTRANS = :row_id";
        $stmt = $db->prepare($query);
        $stmt->bindValue(":new_status", $newStatus);
        $stmt->bindValue(":row_id", $row_id_htrans);
        $result = $stmt->execute();
    }

    if (isset($_POST['acceptOrder'])) {
        updateTransactionStatus($db, $_POST['row_id_htrans'], 1);
        refreshPage();
        // showAlertModal('bg-success', '<i class="fas fa-exclamation-triangle"></i>', '<h4>Status Has Been Changed</h4>', 'Close', '');
    }

    if (isset($_POST['rejectOrder'])) {
        updateTransactionStatus($db, $_POST['row_id_htrans'], 2);
        refreshPage();
        // showAlertModal('bg-success', '<i class="fas fa-exclamation-triangle"></i>', '<h4>Status Has Been Changed</h4>', 'Close', '');
    }

?>

<!doctype html>
<html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

        <!-- CSS Library Import -->
        <link rel="stylesheet" href="css/bootstrap.min.css">
        <link rel="stylesheet" type="text/css" href="css/jquery-ui.css">
        <link rel="stylesheet" type="text/css" href="css/datatables.css"/>
        <link href="css/all.css" rel="stylesheet">
        <link rel="icon" type="image/png" href="res/img/goblin.png" />    
         
        <!-- JS Library Import -->
        <script src="js/jquery-3.4.1.min.js"></script>
        <script src="js/bootstrap.bundle.min.js"></script>
        <script src="js/jQueryUI.js"></script>
        <script type="text/javascript" src="js/datatables.js"></script>
        <script src="script/index.js"></script>

        <!-- CSS Sendiri -->
        <link href="style/index.css" rel="stylesheet">
        <style>
            /* .footer {
                position: absolute;
                bottom: 0;
                width: 100%;
            } */

            #judul{
                padding: 0;
                padding-top: 5%;

                padding-bottom: 5%;
                background-repeat:no-repeat;
                background-size: cover;
            }
        </style>

        <!-- JS Sendiri -->
        <title>Transaction Detail</title>
    </head>
    <body id="page-top">
        <!-- Header Section -->
        <?php include ("header.php"); ?>

        <!-- Main Section -->
        <main>
            <!-- kalau mau pake space ga ush dicomment -->
            <!-- <div class="spaceatas"></div> -->
            <div id="judul" class="col-12 text-center my-5" style="background-image: url('res/img/bg12.jpg');">
                <h1 class="text-light display-3 font-weight-bold">
                    Transaction List
                </h1>
            </div>

            <!-- container -> jarak ikut bootstrap, container-fluid -> jarak full width, w-(ukuran) -> sesuai persentase, contoh w-80 -> 80% -->
            <div class="container">
                <?php showTransactionList($db,$jenisUser, $rowIdUserAktif); ?>
            </div>
        </main>

        <!-- Footer Section -->
        <?php include ("footer.php"); ?>

        <?php
            if (isset($_POST['acceptOrder'])) {
                showAlertModal('bg-success', '<i class="fas fa-exclamation-triangle"></i>', '<h4>Status Has Been Changed</h4>', 'Close', '');
            }
        
            if (isset($_POST['rejectOrder'])) {
                showAlertModal('bg-success', '<i class="fas fa-exclamation-triangle"></i>', '<h4>Status Has Been Changed</h4>', 'Close', '');
            }
            
        ?>
    </body>
</html>