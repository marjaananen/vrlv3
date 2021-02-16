<h1>Omat hevoset</h1>

 <ul class="nav nav-tabs">
        <li role="presentation" class="<?php if ($sivu == 'omat' || empty($sivu)){echo "active";}?>"><a href="<?php echo base_url('virtuaalihevoset/omat')?>">Kaikki</a></li>
        <li role="presentation" class="<?php if ($sivu == 'uudet'){echo "active";}?>"><a href="<?php echo base_url('virtuaalihevoset/vastarekisteroidyt')?>">Uudet</a></li>
 </ul>
 
 <?php echo $data;?>