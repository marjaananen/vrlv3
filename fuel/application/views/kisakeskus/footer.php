	
</div>

<div style="clear:both;text-align:center;font-size:11px;padding-top:30px;">
Ulkoasu &copy; 2013 <a href="http://raitatossu.net/mayflower">M Layouts</a>
</div>	
</div>
  <!-- Placed at the end of the document so the pages load faster -->
    <!-- Bootstrap core JavaScript
    ================================================== -->
    <!-- Placed at the end of the document so the pages load faster -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.2/js/bootstrap.min.js"></script>
    <!-- IE10 viewport hack for Surface/desktop Windows 8 bug -->
    <script src="../../assets/js/ie10-viewport-bug-workaround.js"></script>

  
<!-- DataTables -->
<script type="text/javascript" charset="utf8" src="//cdn.datatables.net/1.10.5/js/jquery.dataTables.js"></script>
<script type="text/javascript" charset="utf8" src="//cdn.datatables.net/plug-ins/f2c75b7247b/integration/bootstrap/3/dataTables.bootstrap.js"></script>
<script type="text/javascript" charset="utf8" src="//cdnjs.cloudflare.com/ajax/libs/moment.js/2.8.4/moment.min.js"></script>
<script type="text/javascript" charset="utf8" src="//cdn.datatables.net/plug-ins/f2c75b7247b/sorting/datetime-moment.js"></script>


<script type="text/javascript" charset="utf-8">
$(document).ready( function () {
    
    $.fn.dataTable.moment( 'DD.MM.YYYY' );
    $.fn.dataTable.moment( 'DD.MM.YYYY' );
    
    $('#lista').DataTable({
        "order": [[ 0, "desc" ]],
        "lengthMenu": [ 25, 50, 75, 100 ]
        });
    
    

} );

</script>


  </body>
</html>