<?php
    include "../../../admincp/config/config.php"; 
    require("../../../mail/sendmail.php");
    require_once('config_vnpay.php');
    session_start();

    if (!isset($_SESSION['id_khachhang'])) {
        die("Lỗi: Bạn cần đăng nhập để đặt hàng.");
    }

    $id_customer = $_SESSION['id_khachhang'];
    $code_cart = rand(10000, 99999);
    $_SESSION['code_cart']=$code_cart;
    $id_khuyenmai = isset($_GET['promotion_id']) ? (int) $_GET['promotion_id'] : 0;
    $_SESSION['id_khuyenmai']=$id_khuyenmai;
    $thanhtoan=$_POST['payment'];
    $_SESSION['thanhtoan']=$thanhtoan;
    $result = mysqli_query($mysqli, "SELECT * FROM tbl_vanchuyen WHERE id_dangky = '$_SESSION[id_khachhang]'");

    if ($result) {
        // Lấy kết quả từ truy vấn
        $row = mysqli_fetch_assoc($result);

        // Kiểm tra nếu có dữ liệu
        if ($row) {
            $id_vanchuyen = $row['id_vanchuyen'];
        }
    }

    if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
        die("Lỗi: Giỏ hàng của bạn đang trống!");
    }
    // Tính tổng tiền
    $tong_tien = 0;
    $chi_tiet_don_hang = "";
    foreach ($_SESSION['cart'] as $key => $value) {
        $quantity = $value['soluong'];
        $price = $value['giasp']; 
        $total = $quantity * $price;
        $tong_tien += $total;
    }
    // Áp dụng khuyến mãi nếu có
    $giam_gia = 0;
    if ($id_khuyenmai > 0) {
        $sql_km = "SELECT * FROM tbl_khuyenmai WHERE id_khuyenmai = '$id_khuyenmai'";
        $query_km = mysqli_query($mysqli, $sql_km);
        if ($row_km = mysqli_fetch_assoc($query_km)) {
            $loai_km = $row_km['loai_khuyenmai']; // 'phantram' hoặc 'codinh'
            $giatri_km = $row_km['giatri'];
            $toithieu_km = $row_km['toithieu_khuyenmai'];

            // Kiểm tra nếu tổng tiền đủ điều kiện giảm giá
            if ($tong_tien >= $toithieu_km) {
                if ($loai_km == 'phantram') {
                    $giam_gia = ($tong_tien * $giatri_km) / 100;
                } else if ($loai_km == 'codinh') {
                    $giam_gia = $giatri_km;
                }
            }
        }
    }

    // Tính tổng tiền sau khi giảm giá
    $tong_sau_giam = max(0, $tong_tien - $giam_gia);
    $_SESSION['tong_tien']=$tong_sau_giam ;
    // th thanh toán
    if($thanhtoan=='tienmat' || $thanhtoan=='chuyenkhoan')
    {
        $sql = "INSERT INTO tbl_giohang(id_khachhang, ma_giohang, trangthai_giohang, id_khuyenmai, ngay_mua, pt_thanhtoan, id_vanchuyen, tongtien) 
                VALUES ('$id_customer', '$code_cart', '1', '$id_khuyenmai',NOW(),'$thanhtoan','$id_vanchuyen','$tong_sau_giam')";
        $sql_query = mysqli_query($mysqli, $sql);

        if (!$sql_query) {
            die("Lỗi SQL khi tạo giỏ hàng: " . mysqli_error($mysqli));
        }
        foreach ($_SESSION['cart'] as $key => $value) {
            $tensanpham = $value['tensanpham'];
            $id_sp = $value['id'];
            $quantity = $value['soluong'];
            $price = $value['giasp']; 
            $total = $quantity * $price;
            $sql_detail = "INSERT INTO tbl_giohang_chitiet(ma_giohang, id_sanpham, soluong) 
                        VALUES ('$code_cart', '$id_sp', '$quantity')";
            if (!mysqli_query($mysqli, $sql_detail)) {
                die("Lỗi khi thêm sản phẩm vào giỏ hàng: " . mysqli_error($mysqli));
            }

            $chi_tiet_don_hang .= "
                <tr>
                    <td style='padding:10px;border:1px solid #ddd;text-align:center;'>$tensanpham</td>
                    <td style='padding:10px;border:1px solid #ddd;text-align:center;'>$quantity</td>
                    <td style='padding:10px;border:1px solid #ddd;text-align:center;'>".number_format($price, 0, ',', '.')." VND</td>
                    <td style='padding:10px;border:1px solid #ddd;text-align:center;color:#d9534f;font-weight:bold;'>
                        ".number_format($total, 0, ',', '.')." VND
                    </td>
                </tr>";
        }
        
    }elseif($thanhtoan=='momo'){
        echo "momo";
    }elseif($thanhtoan=='vnpay'){
        $vnp_TxnRef = $code_cart; //Mã giao dịch thanh toán tham chiếu của merchant
        $vnp_Amount = round($tong_sau_giam)*10; 
        $vnp_Locale = 'vn'; //Ngôn ngữ chuyển hướng thanh toán
        $vnp_BankCode = 'NCB'; //Mã phương thức thanh toán
        $vnp_IpAddr = $_SERVER['REMOTE_ADDR']; //IP Khách hàng thanh toán

        $inputData = array(
            "vnp_Version" => "2.1.0",
            "vnp_TmnCode" => $vnp_TmnCode,
            "vnp_Amount" => $vnp_Amount * 10,
            "vnp_Command" => "pay",
            "vnp_CreateDate" => date('YmdHis'),
            "vnp_CurrCode" => "VND",
            "vnp_IpAddr" => $vnp_IpAddr,
            "vnp_Locale" => $vnp_Locale,
            "vnp_OrderInfo" => "Thanh toan GD: " . $vnp_TxnRef,
            "vnp_OrderType" => "other",
            "vnp_ReturnUrl" => $vnp_Returnurl,
            "vnp_TxnRef" => $vnp_TxnRef,
            "vnp_ExpireDate"=>$expire
        );

        if (isset($vnp_BankCode) && $vnp_BankCode != "") {
            $inputData['vnp_BankCode'] = $vnp_BankCode;
        }

        ksort($inputData);
        $query = "";
        $i = 0;
        $hashdata = "";
        foreach ($inputData as $key => $value) {
            if ($i == 1) {
                $hashdata .= '&' . urlencode($key) . "=" . urlencode($value);
            } else {
                $hashdata .= urlencode($key) . "=" . urlencode($value);
                $i = 1;
            }
            $query .= urlencode($key) . "=" . urlencode($value) . '&';
        }

        $vnp_Url = $vnp_Url . "?" . $query;
        if (isset($vnp_HashSecret)) {
            $vnpSecureHash =   hash_hmac('sha512', $hashdata, $vnp_HashSecret);//  
            $vnp_Url .= 'vnp_SecureHash=' . $vnpSecureHash;
        }
        header('Location: ' . $vnp_Url);
        die();
    }else{
        echo "paypal";
    }
    if (!isset($_SESSION['mail']) || empty($_SESSION['mail'])) {
        // Skip email if no address, but don't block the order
        error_log("Warning: No email address for order confirmation");
    } else {
        $maildathang = $_SESSION['mail'];

        // Nội dung email
        $tieude = "LuxuryStore_Bạn đã đặt hàng thành công";
        $noidung = "
        <!DOCTYPE html>
        <html lang='vi'>
        <head>
            <meta charset='UTF-8'>
            <meta name='viewport' content='width=device-width, initial-scale=1.0'>
            <title>Mail đơn hàng</title>
        </head>
        <body>
            <p>Xin chào <b>" . $_SESSION['dangky'] . "</b>,</p>
            <p>Bạn đã đặt hàng thành công tại Luxury Store.</p>
            <table border='1' cellspacing='0' cellpadding='10'>
                <tr>
                    <th>Sản phẩm</th>
                    <th>Số lượng</th>
                    <th>Giá</th>
                    <th>Tổng</th>
                </tr>
                $chi_tiet_don_hang
                <tr>
                    <td colspan='3'><b>Tổng cộng:</b></td>
                    <td>".number_format($tong_tien, 0, ',', '.')." VNĐ</td>
                </tr>
                <tr>
                    <td colspan='3'><b>Giảm giá:</b></td>
                    <td style='color:red;'>- ".number_format($giam_gia, 0, ',', '.')." VNĐ</td>
                </tr>
                <tr>
                    <td colspan='3'><b>Tổng sau giảm:</b></td>
                    <td style='color:#d9534f;font-weight:bold;'>".number_format($tong_sau_giam, 0, ',', '.')." VNĐ</td>
                </tr>
            </table>
            <p>Chúng tôi sẽ giao hàng sớm nhất!</p>
        </body>
        </html>";

        // Try to send email but don't block if it fails
        try {
            $mail = new Mailer();
            $mail->dathangmail($tieude, $noidung, $maildathang);
        } catch (Exception $e) {
            // Log error but continue - don't block user
            error_log("Email send failed: " . $e->getMessage());
        }
    }

    // Xóa giỏ hàng sau khi đặt hàng thành công
    unset($_SESSION['cart']);
    
    echo "<script>
        alert('Bạn đã đặt hàng thành công! Cảm ơn quý khách đã đặt hàng.');
        window.location.href = '../../../index.php?quanly=giohang&buoc=donhangdadat';
      </script>";
?>
