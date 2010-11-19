<?php include 'globalheader.php';?>
    <script type="text/javascript">
    $("document").ready(function() {
            $("#jplr1").jPlayer({
                    volume: 0
            });
    });    
    </script>
    <body>
        <div id="alert" class="alert">
            <span>
                <?php echo $alert; ?>
            </span>
        </div>
        <div id="jplr1" name="jplr1"></div>
        <div class="head stripe">
            <a href="./staff.php">
                Musiccn
            </a>
        </div>
