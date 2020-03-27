<?php
/*
This is the template to show the data from the API.
 */
declare(strict_types=1);
?>
<!doctype html>
<html lang="en">
<head>
    <title><?php echo esc_html__('Inpsyde Sample Plugin', 'ssinpsyde')?></title>
    <meta http-equiv="content-type" content="text/html; charset=UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
<?php
wp_head();
$ssArrUsersDetails=$this->ssFnSendRequest('ssArrUsersDetails');
?>
</head>
<body class="ss-body">
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container d-flex justify-content-center">
        <a class="navbar-brand" href="#"><?php echo esc_html__('User details', 'ssinpsyde');?></a>
    </div>
</nav>
<main role="main" class="container">

    <div class="table-responsive">
        <table class="table table-hover">
            <thead>
            <tr>
                <th scope="col"><?php echo esc_html__('Id', 'ssinpsyde');?></th>
                <th scope="col"><?php echo esc_html__('Username', 'ssinpsyde');?></th>
                <th scope="col"><?php echo esc_html__('Name', 'ssinpsyde');?></th>
                <th scope="col"><?php echo esc_html__('Email', 'ssinpsyde');?></th>
                <th scope="col"><?php echo esc_html__('Phone', 'ssinpsyde');?></th>
                <th scope="col"><?php echo esc_html__('Website', 'ssinpsyde');?></th>
            </tr>
            </thead>
            <tbody>
            <?php
            if (is_array($ssArrUsersDetails) && count($ssArrUsersDetails)>0) {
                foreach ($ssArrUsersDetails as $ssRow) {
                    $ssGeo='//maps.google.com/maps?z=14&q=';
                    $ssGeo.=$ssRow['address']['geo']['lng'].','.$ssRow['address']['geo']['lat'];
                    ?>
                    <tr>
                        <th scope="row">
                            <a  data-toggle="modal" data-name="<?php echo esc_html($ssRow['name']);?>"
                                href=".ss-modal" data-userid="<?php echo esc_html($ssRow['id']);?>">
                                <?php echo esc_html($ssRow['id']);?>
                            </a>
                        </th>
                        <td>
                            <a  data-toggle="modal" data-name="<?php echo esc_html($ssRow['name']);?>"
                                href=".ss-modal" data-userid="<?php echo esc_html($ssRow['id']);?>">
                                <?php echo esc_html($ssRow['username']);?>
                            </a>
                        </td>
                        <td>
                            <a  data-toggle="modal" data-name="<?php echo esc_html($ssRow['name']);?>"
                                href=".ss-modal" data-userid="<?php echo esc_html($ssRow['id']);?>">
                                <?php echo esc_html($ssRow['name']);?>
                            </a>
                        </td>
                        <td>
                            <a class="badge badge-light" href="mailto:<?php echo esc_html($ssRow['email']);?>">
                                <?php echo esc_html($ssRow['email']);?>
                            </a>
                        </td>
                        <td>
                            <a class="badge badge-light" href="tel:<?php echo esc_html($ssRow['phone']);?>">
                                <?php echo esc_html($ssRow['phone']);?>
                            </a>
                        </td>
                        <td>
                            <a class="badge badge-light" href="//<?php echo esc_html($ssRow['website']);?>" target="_blank">
                                <?php echo esc_html($ssRow['website']);?>
                            </a>
                        </td>
                    </tr>
                    <?php
                }
            }
            if ($ssArrUsersDetails==='') {
                echo '<tr><td colspan="7">'
                    .esc_html__('It seems like problem in API connection, Please try again.', 'ssinpsyde').
                    '</td></tr>';
            }
            ?>
            </tbody>
        </table>
    </div>
</main>
<footer class="footer">
    <div class="container d-flex justify-content-center">
        <span class="text-muted">&copy;
            <?php echo get_the_date('Y').esc_html__(' by Sagar Shinde, All rights reserved', 'ssinpsyde');?>
        </span>
    </div>
</footer>
<!-- Modal -->
<div class="modal fade ss-modal" id="ssModelCenter" tabindex="-1" role="dialog"
     aria-labelledby="ssModelCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable modal-dialog-centered modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="ssModelCenterTitle">
                    <?php echo esc_html__('User Posts', 'ssinpsyde');?>
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="container-fluid">
                    <div class="ss-loader d-flex justify-content-center">
                        <div class="spinner-border m-5" role="status">
                            <span class="sr-only"><?php echo esc_html__('Loading...', 'ssinpsyde')?></span>
                        </div>
                    </div>
                    <div class="ssContent">

                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

</body>
<?php
wp_footer();
?>
</html>