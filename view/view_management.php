<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title><?= $member->username ?>'s Profile!</title>
        <base href="<?= $web_root ?>"/>
        <link href="css/styles.css" rel="stylesheet" type="text/css"/>

        <script src="lib/jquery-3.4.0.min.js" type="text/javascript"></script>
        <script src="lib/url-tools-bundle.min.js"></script>

        <link
            href="lib/jquery-ui-1.12.1.ui-lightness/jquery-ui.min.css"
            rel="stylesheet"
            type="text/css"/>
        <link
            href="lib/jquery-ui-1.12.1.ui-lightness/jquery-ui.theme.min.css"
            rel="stylesheet"
            type="text/css"/>
        <link
            href="lib/jquery-ui-1.12.1.ui-lightness/jquery-ui.structure.min.css"
            rel="stylesheet"
            type="text/css"/>
        <script
            src="lib/jquery-ui-1.12.1.ui-lightness/jquery-ui.min.js"
            type="text/javascript"></script>

        <link href='lib/node_modules/@fullcalendar/core/main.css' rel='stylesheet'/>
        <link href='lib/node_modules/@fullcalendar/timeline/main.css' rel='stylesheet'/>
        <link href='lib/node_modules/@fullcalendar/daygrid/main.css' rel='stylesheet'/>
        <link
            href='lib/node_modules/@fullcalendar/resource-timeline/main.css'
            rel='stylesheet'/>

        <script src='lib/node_modules/@fullcalendar/core/main.js'></script>
        <script src='lib/node_modules/@fullcalendar/timeline/main.js'></script>
        <script src='lib/node_modules/@fullcalendar/resource-common/main.js'></script>
        <script src='lib/node_modules/@fullcalendar/resource-timeline/main.js'></script>
        <script src='lib/node_modules/@fullcalendar/daygrid/main.js'></script>       
     
        <script>
            
            var admin ="<?=$member->role?>";
            today = new Date();
            document.addEventListener('DOMContentLoaded', function () {
                
                var calendarEl = document.getElementById('calendar');
                var calendar = new FullCalendar.Calendar(calendarEl, {
                    
                    schedulerLicenseKey: 'GPL-My-Project-Is-Open-Source',
                    plugins: ['resourceTimeline', 'interaction'],
                    defaultView: 'month',
                    displayEventTime: false,
                    
                    header: {
                        left: 'today,prev,next',
                        center: 'title',
                        right: 'week,month,year'
                    },        
                    firstDay : 1,
                    aspectRatio: 2.5,
                    editable: true,
                    views: {
                        week: {
                            
                            type: 'resourceTimeline',
                            duration: {
                                week:1
                            },

                            slotDuration: {
                                days: 1
                            },
                            buttonText: 'Week',
                            slotLabelFormat: [
                                {
                                    weekday: 'short',
                                    day: '2-digit'
                                }
                            ]
                        },
                        month: {
                            type: 'resourceTimeline',
                            duration: {
                                months: 1
                            },
                            buttonText: 'Month',
                            slotLabelFormat: [
                                {
                                    day: 'numeric'
                                }
                            ]
                        },
                        year: {
                            type: 'resourceTimeline',
                            duration: {
                                years: 1
                            },
                            slotDuration: {
                                months: 1
                            },
                            buttonText: 'Year'
                        }
                    },
                    resourceColumns: [
                        {
                            labelText: 'User',
                            field: 'fuser',
                           width : 45
                        }, {
                            labelText: 'Book',
                            field: 'fbook'
                        }
                    ],
                    refetchResourcesOnNavigate: true,
                    filterResourcesWithEvents:true,
                    resources: {
                        url: 'rental/ressourceJson',
                        method: 'POST',
                        extraParams : function(){

                            var open = $('#open').is(':checked');
                                var returns = $('#return').is(':checked');
                                var all = $('#all').is(':checked');
                                
                                var status = 'open';
                                if (returns){
                                    status = 'return';
                                } else if (all){
                                    status = 'all';
                                };
                        
                            return { member : $('#user').val(),
                                    book : $('#book').val(),
                                    date : $('#date').val(),
                                    state : status
                                    }
         
        }
    
    },
    
                    events: {
                        url: 'rental/eventsJson',
                        method: 'POST',
                        data: function() {
                        return {
                            rentalID: $('#rentalID').val()
                    };
                },                       
                    },
                    
                    eventClick: function (data) {
                        dateStart = new Date(data.event.start);
                        dStart = dateStart.getDate() + '/' + (
                            dateStart.getMonth() + 1
                        ) + '/' + dateStart.getFullYear();
                        dateEnd = new Date(data.event.end);
                        dEnd = dateEnd.getDate() + '/' + (
                            dateEnd.getMonth() + 1
                        ) + '/' + dateEnd.getFullYear();
                                       
                        var toTest = data.event.extendedProps.getColorTxt;  
                        
                        if (toTest == '#00d67c'){
                            dEnd ='not return yet';
                        }
                        var user = data.event.extendedProps.user;
                        var book = data.event.extendedProps.book;
                        var author = data.event.extendedProps.author;
                        var id = data.event.id;
                        $('#dialog').dialog('open');
                        $("#eventAuthor").text(author);
                        $("#eventBook").text(book);
                        $("#eventUser").text(user);
                        $("#eventStart").text(dStart);
                        $("#eventEnd").text(dEnd);
                        $('#rentalID').text(id);
                        $('btnToHide').text(toTest);                       
                    },
                    resourceOrder: 'start',
                    
                });

                calendar.render();
                
               

                
            $('#user, #book, #date, #open, #return, #all').on("input", function () {
                                calendar.refetchEvents();
                                calendar.refetchResources();
                                

                                
                
            });
        

            
            $(function () {
                $("#dialog").dialog({                    
                    autoOpen: false,
                    resizable: false,
                    height: "auto",
                    width: 800,
                    modal: true,
                    buttons: {                       
                    <?php  if ($member->role == 'admin' ) : ?> 
                        'delete': function () {        
                            $.post("rental/delete_service", 
                                {rentalID: parseInt($('#rentalID').text(),10)},
                                actionRefetch, "html");
                                calendar.refetchEvents();
                                calendar.refetchResources();
                                $(this).dialog("close");       
                        },
                        <?php endif;?>
                            
                                
                         'return': function () {  
                           $.post('rental/return_service',
                           {rentalID: parseInt($('#rentalID').text(),10)},
                           actionRefetch,"html");
                           calendar.refetchEvents();
                           calendar.refetchResources();
                           $(this).dialog("close");
     
                        
                    },
                        'close': function () {
                            $(this).dialog("close");
                        }
                    }
                });
            });
        });
            $(function () {
                $("#tabForm").hide()
            });
            function actionRefetch(data) {
                console.log(data);
            };


            $(function () {
                
                $('#toDesabled').attr('disabled', true);       
                $("#btnToHide").hide();
         
                
            });
        </script>
    </head>
    <body>  

        <div id="dialog" title="Rental Details" hidden="hidden">
            <div id ='hidden' type ="text"></div>
            <div id='rentalID' type="text"  hidden></div>
            <h1>user:
                <strong>
                    <span id='eventUser'></span></strong>
            </h1>
            <h1>book:
                <span id='eventBook'></span>
                (<span id='eventAuthor'></span>)
            </h1>
            <h1>Rental date:
                <span id='eventStart'></span></h1>
            <h1>Return date:
                <span id='eventEnd'></span></h1>

        </div>
        <?php include('menu.php'); ?>
        <div class="container">
            <header>
                <h2>Returns Management</h2>
                <?php if (isset($_GET['param1'])) 
            $filter = Utils::url_safe_decode($_GET['param1'])?>
            </header>
            <h3>Filter
            </h3>
            <form action='rental/returns_management' method='POST'>
                <input
                    type='text'
                    class ='myInput'
                    id ="user"
                    name='memberS'
                    placeholder=' Member '
                    value="<?=$filter['memberS'] ?>">
                <input
                    type='text'
                    name='bookS'
                    id ="book"
                    class ='myInput'
                    placeholder=' Book '
                    value='<?=  $filter['bookS']?>'>
                <br>
                <div class='date'>
                    <input type="date" name='dateS' id ="date"class ='myInput' value='<?=  $filter['dateS']?>'>
                </div>
                <div class='radiobtn'>
                    <input
                        type='radio'
                        name='stateS'
                        id ="open"
                        class ='status'
                        value="open"
                        <?php if ($filter['stateS'] == 'open' ) :?>
                        <?php endif;?>checked="checked" >
                    Open
                    <input
                        type='radio'
                        class ='status'
                        name='stateS'
                        id="return"
                        value="return"
                        <?php if ($filter['stateS'] == 'return' ) :?>
                        <?php endif;?>checked="checked" >
                    Returned
                    <input
                        type='radio'
                        name='stateS'
                        id="all"
                        class ='status'
                        value="all"
                        <?php if ($filter['stateS'] == 'all' ) :?>
                        <?php endif;?>checked="checked" >
                    All
                </div>
                
                <div id='btnToHide'> 
               <br>
                <input type='submit' name='search'  id="toDesabled" value='Apply filter'>
                <?php if (isset($_GET['param1'])): ?>
                <a href="rental/management_returns">
                    <input type="button" value="Clear">
                </a>
                <?php endif; ?>
                <br><br>
                </div>
                <?php 
                    $rentFilter= Rental::search($filter);
                            if (isset($rentFilter))
                                $rentals = $rentFilter;
                            ?>
                <div id='tabForm'>
                    <div class="listTabR" id="rentalList">
                        <table>
                            <thead>
                                <tr>
                                    <th width="15%">Rental Date / time</th>
                                    <th width="13.5%">Member</th>
                                    <th width="45%">Book</th>
                                    <th width="15%">To be returned on</th>
                                    <th width="13%">Actions</th>
                                </tr>
                            </thead>
                            <tbody id='myTable'>
                                <?php foreach ($rentals as $r) : ?>
                                <tr>
                                    <td id="rentaldate">
                                        <?=$r->rentaldate ?></td>
                                    <td id="rentalUsername">
                                        <?= Member::get_name($r->user) ?></td>
                                    <td id="rentalBook">
                                        <?= Book::get_title($r->book) ?></td>
                                    <td>
                                        <?php
                                        
                                        if (Rental::isLate($r->rentaldate))
                                            echo '<div class="datePast">'  ;                          
                                        ?>
                                        <?= $r->returndate ?>
                                        <?php if (Rental::isLate($r->rentaldate) )  echo "</div>" ?>
                                    </td>
                                    <td><?php if ($member->role === 'admin') : ?>
                                        <input
                                            type="submit"
                                            formaction="rental/gestion/<?= $r->id?>"
                                            class='delIcon'
                                            name="delete"
                                            value=''>
                                        <?php endif; ?>
                                        <input
                                            type="submit"
                                            formaction="rental/gestion/<?= $r->id?>"
                                            class='createReturn'
                                            name='return'
                                            value=''>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </form>
            <br>
            <div id='calendar'></div>
        </div>
    </body>
</html>