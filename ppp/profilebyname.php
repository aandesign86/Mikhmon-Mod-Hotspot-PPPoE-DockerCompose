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

  // Ambil langsung parameter ID unik profile dari URL tanpa merusak routing utama
  if (isset($_GET['id'])) {
      $prof_id = $_GET['id'];
  } else {
      $prof_id = "";
  }

  if (substr($prof_id, 0, 1) == "*") {
    $prof_id = $prof_id;
  } elseif ($prof_id != "") {
    $getprofile = $API->comm("/ppp/profile/print", array(
      "?name" => "$prof_id",
    ));
    if (!empty($getprofile[0]['.id'])) {
        $prof_id = $getprofile[0]['.id'];
    }
  }

  $getbridge = $API->comm("/interface/bridge/print");

  $getprofile = $API->comm("/ppp/profile/print", array(
    "?.id" => "$prof_id"
  ));

  if (empty($getprofile)) {
    echo "<div class='row'><div class='col-12'><b>Profile not found atau ID tidak valid.</b></div></div>";
  } else {
    $profiledetalis = $getprofile[0];
    $pid = $profiledetalis['.id'];
    $pname = $profiledetalis['name'];
    $localaddress = $profiledetalis['local-address'];
    $remoteaddress = $profiledetalis['remote-address'];
    $bridge = $profiledetalis['bridge'];
    $ratelimit = $profiledetalis['rate-limit'];
    $onlyone = $profiledetalis['only-one'];
    $bridgeportpriority = $profiledetalis['bridge-port-priority'];
    $bridgepathcost = $profiledetalis['bridge-path-cost'];
    $bridgehorizon = $profiledetalis['bridge-horizon'];
    $incomingfilter = $profiledetalis['incoming-filter'];
    $outgoingfilter = $profiledetalis['outgoing-filter'];
    $addresslist = $profiledetalis['address-list'];
    $interfacelist = $profiledetalis['interface-list'];
    $dnsserver = $profiledetalis['dns-server'];
    $winsserver = $profiledetalis['wins-server'];
    $changetcp = $profiledetalis['change-tcp-mss'];
    $useupnp = $profiledetalis['use-upnp'];

    if (isset($_POST['name'])) {
      $name = (preg_replace('/\s+/', '-', $_POST['name']));
      $localaddress = ($_POST['localaddress']);
      $remoteaddress = ($_POST['remoteaddress']);
      $bridge = ($_POST['bridge']);
      $ratelimit = ($_POST['ratelimit']);
      $onlyone = ($_POST['onlyone']);
      $incomingfilter = ($_POST['incomingfilter']);
      $outgoingfilter = ($_POST['outgoingfilter']);
      $addresslist = ($_POST['addresslist']);
      $interfacelist = ($_POST['interfacelist']);
      $dnsserver = ($_POST['dnsserver']);
      $winsserver = ($_POST['winsserver']);
      $changetcp = ($_POST['changetcp']);
      $useupnp = ($_POST['useupnp']);

      if ($bridge != '' || $bridge != NULL) {
        $API->comm("/ppp/profile/set", array(
          ".id" => "$pid",
          "name" => "$name",
          "local-address" => "$localaddress",
          "remote-address" => "$remoteaddress",
          "bridge" => "$bridge",
          "rate-limit" => "$ratelimit",
          "only-one" => "$onlyone",
          "incoming-filter" => "$incomingfilter",
          "outgoing-filter" => "$outgoingfilter",
          "address-list" => "$addresslist",
          "dns-server" => "$dnsserver",
          "wins-server" => "$winsserver",
          "change-tcp-mss" => "$changetcp",
          "use-upnp" => "$useupnp",
        ));
      } else {
        $API->comm("/ppp/profile/set", array(
          ".id" => "$pid",
          "name" => "$name",
          "local-address" => "$localaddress",
          "remote-address" => "$remoteaddress",
          "rate-limit" => "$ratelimit",
          "only-one" => "$onlyone",
          "incoming-filter" => "$incomingfilter",
          "outgoing-filter" => "$outgoingfilter",
          "address-list" => "$addresslist",
          "dns-server" => "$dnsserver",
          "wins-server" => "$winsserver",
          "change-tcp-mss" => "$changetcp",
          "use-upnp" => "$useupnp",
        ));
      }

      // FIX: Menggunakan loadpage AJAX mikhmon agar kembali ke daftar profil dengan mulus tanpa merusak grafik
      echo "<script>window.location.href='./?session=" . $session . "&ppp=profiles';</script>";
	  exit();
    }
?>
<div class="row">
  <div class="col-12">
    <div class="card">
      <div class="card-header">
        <h3><i class="fa fa-edit"></i> Edit PPP Profiles </h3>
      </div>
      <div class="card-body">
        <form autocomplete="off" method="post" action="">
          <div>
            <a class="btn bg-warning" href="./?ppp=profiles&session=<?= $session; ?>"> <i class="fa fa-close"></i> <?= $_close ?></a>
            <button type="submit" name="save" class="btn bg-primary"><i class="fa fa-save"></i> <?= $_save ?></button>
          </div>
          <table class="table">
            <tr>
              <td class="align-middle"><?= $_name ?></td>
              <td><input class="form-control" type="text" onchange="remSpace();" autocomplete="off" name="name" value="<?= $pname; ?>" required="1" autofocus></td>
            </tr>
            <tr>
              <td class="align-middle">Local Address</td>
              <td><input class="form-control" type="text" required="1" size="4" value="<?= $localaddress; ?>" autocomplete="off" name="localaddress"></td>
            </tr>
            <tr>
              <td class="align-middle">Remote Address</td>
              <td><input class="form-control" type="text" required="1" size="4" value="<?= $remoteaddress; ?>" autocomplete="off" name="remoteaddress"></td>
            </tr>
            <?php if (count($getbridge) != 0) { ?>
              <tr>
                <td class="align-middle">Bridge</td>
                <td>
                  <select class="form-control " name="bridge">
                    <?php if ($bridge == '') { ?>
                        <option value="">==Pilih==</option>
                    <?php } else { ?>
                        <option value="<?php echo $bridge; ?>"><?php echo $bridge ?></option>
                    <?php } ?>
                    <?php
                    $TotalReg = count($getbridge);
                    for ($i = 0; $i < $TotalReg; $i++) {
                      echo "<option value='" . $getbridge[$i]['name'] . "'>" . $getbridge[$i]['name'] . "</option>";
                    }
                    ?>
                  </select>
                </td>
              </tr>
            <?php } ?>
            <tr>
              <td class="align-middle">Incoming Filter</td>
              <td>
                <select class="form-control" id="incomingfilter" name="incomingfilter">
                  <option value="" <?= ($incomingfilter == '') ? 'selected' : ''; ?>>== Pilih ==</option>
                  <option value="input" <?= ($incomingfilter == 'input') ? 'selected' : ''; ?>>input</option>
                  <option value="forward" <?= ($incomingfilter == 'forward') ? 'selected' : ''; ?>>forward</option>
                  <option value="output" <?= ($incomingfilter == 'output') ? 'selected' : ''; ?>>output</option>
                </select>
              </td>
            </tr>
            <tr>
              <td class="align-middle">Outgoing Filter</td>
              <td>
                <select class="form-control" id="outgoingfilter" name="outgoingfilter">
                  <option value="" <?= ($outgoingfilter == '') ? 'selected' : ''; ?>>== Pilih ==</option>
                  <option value="input" <?= ($outgoingfilter == 'input') ? 'selected' : ''; ?>>input</option>
                  <option value="forward" <?= ($outgoingfilter == 'forward') ? 'selected' : ''; ?>>forward</option>
                  <option value="output" <?= ($outgoingfilter == 'output') ? 'selected' : ''; ?>>output</option>
                </select>
              </td>
            </tr>
            <tr>
              <td class="align-middle">Address List</td>
              <td><input class="form-control" type="text" size="4" value="<?= $addresslist; ?>" autocomplete="off" name="addresslist"></td>
            </tr>
            <tr>
              <td class="align-middle">DNS Server</td>
              <td><input class="form-control" type="text" size="4" value="<?= $dnsserver; ?>" autocomplete="off" name="dnsserver"></td>
            </tr>
            <tr>
              <td class="align-middle">WINS Server</td>
              <td><input class="form-control" type="text" size="4" value="<?= $winsserver; ?>" autocomplete="off" name="winsserver"></td>
            </tr>
            <tr>
              <td class="align-middle">Change TCP MSS</td>
              <td>
                <select class="form-control" id="changetcp" name="changetcp">
                  <option value="default" <?= ($changetcp == 'default') ? 'selected' : ''; ?>>default</option>
                  <option value="no" <?= ($changetcp == 'no') ? 'selected' : ''; ?>>no</option>
                  <option value="yes" <?= ($changetcp == 'yes') ? 'selected' : ''; ?>>yes</option>
                </select>
              </td>
            </tr>
            <tr>
              <td class="align-middle">Use UPnP</td>
              <td>
                <select class="form-control" id="useupnp" name="useupnp">
                  <option value="default" <?= ($useupnp == 'default') ? 'selected' : ''; ?>>default</option>
                  <option value="no" <?= ($useupnp == 'no') ? 'selected' : ''; ?>>no</option>
                  <option value="yes" <?= ($useupnp == 'yes') ? 'selected' : ''; ?>>yes</option>
                </select>
              </td>
            </tr>
            <tr>
              <td class="align-middle">Rate Limit</td>
              <td><input class="form-control" type="text" required="1" value="<?= $ratelimit; ?>" size="4" autocomplete="off" name="ratelimit" placeholder="example: rx/tx"></td>
            </tr>
            <tr>
              <td class="align-middle">Only One</td>
              <td>
                <select class="form-control" id="onlyone" name="onlyone">
                  <option value="default" <?= ($onlyone == 'default') ? 'selected' : ''; ?>>default</option>
                  <option value="no" <?= ($onlyone == 'no') ? 'selected' : ''; ?>>no</option>
                  <option value="yes" <?= ($onlyone == 'yes') ? 'selected' : ''; ?>>yes</option>
                </select>
              </td>
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