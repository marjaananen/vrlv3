 <ul class="nav nav-tabs">
        <li role="presentation" class="<?php if ($sivu == 'jaokset' || empty($sivu)){echo "active";}?>"><a href="<?php echo base_url('alayhdistykset/tapahtumat/jaokset')?>">Jaosten laatuarvostelut</a></li>
        <li role="presentation" class="<?php if ($sivu == 'kantakirjat'){echo "active";}?>"><a href="<?php echo base_url('alayhdistykset/tapahtumat/kantakirjat')?>">Kantakirjat</a></li>
        <li role="presentation" class="<?php if ($sivu == 'laatuarvostelut'){echo "active";}?>"><a href="<?php echo base_url('alayhdistykset/tapahtumat/laatuarvostelut')?>">Muut laatuarvostelut</a></li>
</ul>
 
 
 <?php echo $lista; ?>