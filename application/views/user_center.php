<div id="page-content" class="home-page" style="margin-bottom: 100px">
    <div style="width: 100%; height: 70px; background-color: #222"></div>
    <div class="container">
        <div style="width: 1140px">
        <?php
            $this->load->view('user_left_side');
        ?>
        <div id="content">
            <form class="form-horizontal" style="padding: 0 40px">
            <?php
                $this->load->view($user_right_side);
            ?>
            </form>
        </div>
        </div>
    </div>
</div>
