<h2><?=$title?></h2>

<p>
    <div class="alert alert-<?php echo fuel_var('msg_type', 'info')?>" role="alert">   
        <?php echo fuel_var('msg', '')?>
        <?php echo validation_errors(); ?>
    </div>
    
    <?php
        echo fuel_var('form', '');
    ?>
    
    <br />
    
    <div id="result_div"></div>
    
    <?php if(!empty($headers)) : ?>
        <script>
            $(document).ready(function() {
                var headers = JSON.parse('<?=$headers?>');
                var data = JSON.parse('<?=$data?>');
                var numcolumns = 0;
                var table = "<table id='result_table'><thead><tr>";
                var prepend_text;

                for(i in headers)
                {
                    table += "<th>" + headers[i]['title'] + "</th>";
                    numcolumns++;
                }
                
                table += "</tr></thead><tbody>";
                
                for(d in data)
                {
                    if (data[d] == null)
                        continue;
                    
                    table += "<tr>";
                    
                    for(var i=1; i<=numcolumns; i++)
                    {
                        if (headers[i]['prepend_text'] != undefined)
                            prepend_text = headers[i]['prepend_text'];
                        else
                            prepend_text = "";
                        
                        //Jos pitää koota kaikki saman hakukohteen useat arvot yhteen arvoon, mennään iffiin
                        //esim. tallihaussa kootaan yhteen muuttujaan kaikki tallin kategoriat
                        if(headers[i]['aggregated_by'] != undefined)
                        {
                            for(agr in data) //etsitään muut aggregoitavat rivit
                            {
                                if (data[agr] == null)
                                    continue;
                                
                                //eihän ole sama rivi kuin mitä tulostetaan && vastaahan yksilöivät ID:t toisiaan (aggregated_by:t siis kertovat ID:n keyn)
                                if (d != agr && data[agr][headers[i]['aggregated_by']] == data[d][headers[i]['aggregated_by']])
                                {
                                    data[d][headers[i]['key']] += ", " + data[agr][headers[i]['key']]; //appendaus
                                    data[agr] = null; //poistetaan aggregoitu rivi ettei myöhemmin tulosteta sitä uudestaan
                                }
                            }
                        }
                        
                        if (headers[i]['profile_link'] != undefined)
                            table += "<td><a href='" + headers[i]['profile_link'] + data[d][headers[i]['key']] + "'>" + prepend_text + data[d][headers[i]['key']] + "</a></td>"; //profiili linkatun arvon tulostus soluunsa
                        else if (headers[i]['date_to_age'] != undefined) {
                            if (data[d][headers[i]['key']] == "0000-00-00") {
                                table += "<td>Ei saatavilla</td>";
                            }
                            else {
                                var t = data[d][headers[i]['key']].split(/[-]/); //date iäksi
                                var date = new Date(t[0], t[1]-1, t[2]);
                                table += "<td>" + _calculateAge(date) + "</td>";
                            }
                        }
                        else
                            table += "<td>" + prepend_text + data[d][headers[i]['key']] + "</td>"; //normaali arvon tulostus soluunsa
                    }
                        
                    table += "</tr>";
                }
                
                table += "<tbody></table>";
                $("#result_div").append(table);
                $('#result_table').DataTable();
            });
            
            function _calculateAge(birthday) { // birthday is a date
                var ageDifMs = Date.now() - birthday.getTime();
                var ageDate = new Date(ageDifMs); // miliseconds from epoch
                return Math.abs(ageDate.getUTCFullYear() - 1970);
            }
        </script>
    <?php endif; ?>
</p>