<?php ob_start(); ?>

<style>
.multipleSelection {
    Display: inline-block;
}

.selectBox {
    position: relative;
}

.selectBox select {
    width: 100%;
    font-weight: bold;
}

.overSelect {
    position: absolute;
    left: 0;
    right: 0;
    top: 0;
    bottom: 0;
}

#checkBoxes {
    display: none;
    border: 1px #8DF5E4 solid;
}

#checkBoxes label {
    display: block;
}

#checkBoxes label:hover {
    background-color: #4F615E;
}
</style>


<div class="button-header">
    <input type="button" value="Add Appointment" class="dashAddAppntmt" data-function="dashAddAppntmt">
</div>


<div class="right-header">
    <div class="multipleSelection">
        <div class="selectBox" onclick="showCheckboxes()">
            <select>
                <option>Choose Calendar(s)...</option>
            </select>
            <div class="overSelect"></div>
        </div>

        <div id="checkBoxes">
            %CALENDARS_SELECT%

        </div>
    </div>
    <input type="button" value="submit" data-function="calendaraccess">
</div>

<div class="calendar">
<div class="row">
    <div class="col-prev-cal">
    <input type="button" value="&lt;&lt; Year" data-function="year-prev">
    <input type="button" value="&lt;&lt; Month" data-function="month-prev">
    <input type="button" value="&lt;&lt; Week" data-function="week-prev">
    </div>
    <div class="col-monthyear-cal">
    <h1>%MONTH_YEAR%</h1>
    </div>
    <div class="col-next-cal">
    <input type="button" value="Week &gt;&gt;" data-function="week-next">
    <input type="button" value="Month &gt;&gt;" data-function="month-next">
    <input type="button" value="Year &gt;&gt;" data-function="year-next">
    </div>
</div>
    <div class="calendar-container">
    <div class="head">
        <div class="time-label"></div>
        <div class="day-label">Sunday</div>
        <div class="day-label">Monday</div>
        <div class="day-label">Tuesday</div>
        <div class="day-label">Wednesday</div>
        <div class="day-label">Thursday</div>
        <div class="day-label">Friday</div>
        <div class="day-label">Saturday</div>
    </div>
   
    <div class="body">
        <div class="time-label">%TIME_LABEL%</div>
        <div class="day-container">%CALENDAR_1%</div>
        <div class="day-container">%CALENDAR_2%</div>
        <div class="day-container">%CALENDAR_3%</div>
        <div class="day-container">%CALENDAR_4%</div>
        <div class="day-container">%CALENDAR_5%</div>
        <div class="day-container">%CALENDAR_6%</div>
        <div class="day-container">%CALENDAR_7%</div>
    </div>
    </div>
</div>

<script type="text/javascript">
var show = true;


    function showCheckboxes() {

        var checkboxes = document.getElementById("checkBoxes");

        if (show) {
            checkboxes.style.display = "block";
            show = false;
        } else {
            checkboxes.style.display = "none";
            show = true;
        }
    }

    $("input[data-function='calendaraccess']").on("click", function(e) {
            e.preventDefault();

            if (e.handled !== true) { 
                calendarlist();
            }
    });

    calendarlist = function (appointment_id) { 

        var calendarlist = [];

        $('input:checkbox[name=calendarlist]:checked').each(function() {
            calendarlist.push($(this).val());
        }); 

        var values = {
			'task' : 'savecalendarcheck'
		};
	
        values.calendarlist = calendarlist;

        $.post( '/handlers/dashboard.php', values, function ( result ) {
        

        load_page('dashboard', {
                'task': 'index',
                'calendarlist' : 1
            });
       
    } );

    }


window.year = %YEAR% ;
window.week = %WEEK% ;

$(document).ready(function() {

    window.calendar_width = $(".calendar .body").width();
    window.calendar_height = $(".calendar .body").height();

    $("input[data-function='dashAddAppntmt']").on("click", function(e) {
        e.preventDefault();

        sessionStorage.removeItem("SessionAddAppntmntDshbrd");

        if (e.handled !== true) {

            sessionStorage.setItem("SessionAddAppntmntDshbrd", true);
            let DshbrdAddAppntmnt = sessionStorage.getItem("SessionAddAppntmntDshbrd");

            load_page('appointments', {
                'task': 'addedit',
                'addAppsavedisable': 1,
                'display_option': true
            }, appointment.form_actions);

            e.handled = true;
        }

    });

    $("input[data-function='week-prev']").on("click", function(e) {
        e.preventDefault();

        if (e.handled !== true) {
            load_page('dashboard', {
                    'task': 'index',
                    'week': window.week - 1,
                    'year': window.year
                }

            );

            e.handled = true;

        }
    });

    $("input[data-function='week-next']").on("click", function(e) {
        e.preventDefault();

        if (e.handled !== true) {
            load_page('dashboard', {
                    'task': 'index',
                    'week': window.week + 1,
                    'year': window.year
                }

            );

            e.handled = true;

        }
    });


    $("input[data-function='month-prev']").on("click", function(e) {
        e.preventDefault();

        if (e.handled !== true) {
            load_page('dashboard', {
                    'task': 'index',
                    'week': window.week - 4,
                    'year': window.year
                }

            );
            e.handled = true;

        }
    });


    $("input[data-function='month-next']").on("click", function(e) {
        e.preventDefault();

        if (e.handled !== true) {
            load_page('dashboard', {
                    'task': 'index',
                    'week': window.week + 4,
                    'year': window.year
                }

            );
            e.handled = true;

        }
    });


    $("input[data-function='year-prev']").on("click", function(e) {
        e.preventDefault();

        if (e.handled !== true) {
            load_page('dashboard', {
                    'task': 'index',
                    'week': window.week,
                    'year': window.year - 1
                }

            );
            e.handled = true;

        }
    });

    $("input[data-function='year-next']").on("click", function(e) {
        e.preventDefault();

        if (e.handled !== true) {
            load_page('dashboard', {
                    'task': 'index',
                    'week': window.week,
                    'year': window.year + 1
                }

            );
            e.handled = true;
        }
    });

});
</script>

<?php $calendar_index = ob_get_clean(); ?>

<?php ob_start(); ?>
<div class="button-header">
    <input type="button" value="Back" class="back">
    <input type="button" value="Save" class="save">
    <input type="button" value="Next Available" data-function="next-available">
</div><!-- /.button-header -->
<div class="errors"></div>
%FORM_APPOINTMENT%

<?php $calendars_addedit = ob_get_clean(); ?>

<?php ob_start(); ?>
<h2>%TITLE%</h2>
<h3>%DATETIME%</h3>
<div class="details">
    <div>
        <span>Calendar</span><span>%CALENDAR%</span>
    </div>
    <div>
        <span>Set by</span><span>%SET_BY%</span>
    </div>
    <div>
        <span>Attendees</span><span>%ATTENDEES%</span>
    </div>
    <div>
        <span>Location</span><span>%LOCATION%</span>
    </div>
    <div>
        <span>Notes</span><span>%NOTES%</span>
    </div>
</div>
<div class="buttons">
    <input type="button" value="Edit" data-function="edit" data-appointmentid="%APPOINTMENT_ID%">
    <input type="button" value="Delete" data-function="delete" data-appointmentid="%APPOINTMENT_ID%">
</div>
<div class="close"><img src="/images/close_x.png" alt="X"></div>

<?php $appointment_view = ob_get_clean(); ?>