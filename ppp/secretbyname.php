<?php
/*
 *  Copyright (C) 2018 Laksamadi Guko.
 *
 *  This program is free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 2 of the License, or
 *  (at your option) any later version.
 *
 *  This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  You should have received a copy of the GNU General Public License
 *  along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */
session_start();
// hide all error
error_reporting(0);
if (!isset($_SESSION["mikhmon"])) {
    header("Location:../admin.php?id=login");
} else {

    $getprofile = $API->comm("/ppp/profile/print");

    // Ambil langsung ID unik (*1, *2, dst) dari parameter URL
    if (isset($_GET['id'])) {
        $secretbyname = $_GET['id'];
    } else {
        $secretbyname = "";
    }

    $getsecret = $API->comm("/ppp/secret/print", array(
        "?.id" => "$secretbyname"
    ));
    
    if (empty($getsecret)) {
        echo "<div class='row'><div class='col-12'><b>Secrets not found atau ID tidak valid.</b></div></div>";
    } else {
        $secretdetail = $getsecret[0];
        $sid = $secretdetail['.id'];
        $sname = $secretdetail['name'];
        $password = $secretdetail['password'];
        $service = $secretdetail['service'];
        $callerid = $secretdetail['caller-id'];
        $profile = $secretdetail['profile'];

        // FIX: Mencegah error jika schedulerbyname kosong
        if (!empty($schedulerbyname)) {
            $getsch = $API->comm("/system/scheduler/print", array(
                "?name" => "$schedulerbyname"
            ));
            $sechdulerdetail = $getsch[0];
            $schid = $sechdulerdetail['.id'];
            $schname = $sechdulerdetail['name'];
            $schinterval = $sechdulerdetail['interval'];
            $schon = $sechdulerdetail['on-event'];
        }
        
        if (isset($_POST['name'])) {
            $name = (preg_replace('/\s+/', '-', $_POST['name']));
            $password = ($_POST['password']);
            $service = ($_POST['service']);
            $callerid = ($_POST['callerid']);
            $profile = ($_POST['profile']);
            $interval = ($_POST['interval']);

            $API->comm("/ppp/secret/set", array(
                ".id" => "$sid",
                "name" => "$name",
                "password" => "$password",
                "service" => "$service",
                "caller-id" => "$callerid",
                "profile" => "$profile",
            ));

            if (!empty($schid)) {
                $API->comm("/system/scheduler/set", array(
                    ".id" => "$schid",
                    "interval" => "$interval",
                ));
            }
            
            // FIX: Menggunakan redirect JavaScript AJAX loader mikhmon agar halaman langsung diperbarui
            echo "<script>window.location.href='./?session=" . $session . "&ppp=secrets';</script>";
			exit();
        }
?>
<div class="row">
    <div class="col-8">
        <div class="card">
            <div class="card-header">
                <h3><i class="fa fa-edit"></i> Edit PPP Secret </h3>
            </div>
            <div class="card-body">
                <form autocomplete="off" method="post" action="">
                    <div>
                        <a class="btn bg-warning" href="./?ppp=secrets&session=<?= $session; ?>"> <i
                                class="fa fa-close"></i> <?= $_close ?></a>
                        <button type="submit" name="save" class="btn bg-primary"><i class="fa fa-save"></i>
                            <?= $_save ?></button>
                    </div>
                    <table class="table">
                        <tr>
                            <td class="align-middle"><?= $_name ?></td>
                            <td><input class="form-control" type="text" onchange="remSpace();" autocomplete="off"
                                    name="name" value="<?= $sname; ?>" required="1" autofocus></td>
                        </tr>
                        <tr>
                            <td class="align-middle">Password</td>
                            <td><input class="form-control" type="text" size="4" autocomplete="off" name="password"
                                    value="<?= $password; ?>"></td>
                        </tr>
                        <tr>
                            <td class="align-middle">Service</td>
                            <td>
                                <select class="form-control" name="service" required="1">
                                    <option value="any" <?= ($service == 'any') ? 'selected' : ''; ?>>any</option>
                                    <option value="async" <?= ($service == 'async') ? 'selected' : ''; ?>>async</option>
                                    <option value="l2tp" <?= ($service == 'l2tp' || $service == '12tp') ? 'selected' : ''; ?>>l2tp</option>
                                    <option value="ovpn" <?= ($service == 'ovpn') ? 'selected' : ''; ?>>ovpn</option>
                                    <option value="pppoe" <?= ($service == 'pppoe') ? 'selected' : ''; ?>>pppoe</option>
                                    <option value="pptp" <?= ($service == 'pptp') ? 'selected' : ''; ?>>pptp</option>
                                    <option value="sstp" <?= ($service == 'sstp') ? 'selected' : ''; ?>>sstp</option>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <td class="align-middle">Caller ID</td>
                            <td><input class="form-control" type="text" size="4" autocomplete="off" name="callerid"
                                    value="<?= $callerid; ?>"></td>
                        </tr>
                        <tr>
                            <td class="align-middle">Profile</td>
                            <td>
                                <select class="form-control" name="profile" required="1">
                                    <option value="<?php echo $profile; ?>"><?php echo $profile; ?></option>
                                    <?php $TotalReg = count($getprofile);
                                    for ($i = 0; $i < $TotalReg; $i++) {
                                        if($getprofile[$i]['name'] != $profile) {
                                            echo "<option value='" . $getprofile[$i]['name'] . "' >" . $getprofile[$i]['name'] . "</option>";
                                        }
                                    }
                                    ?>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <input type="hidden" name="schid" value="<?php echo $schid; ?>">
                            <td class="align-middle">Interval</td>
                            <td><input class="form-control" value="<?php echo $schinterval; ?>" type="text" size="4"
                                    autocomplete="off" name="interval"></td>
                        </tr>
                    </table>
                </form>
            </div>
        </div>
    </div>
</div>
<?php 
    }
} 
?>