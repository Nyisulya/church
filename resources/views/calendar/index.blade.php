@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="row mb-4 mt-4">
        <div class="col-12">
            <h1 class="m-0 text-dark">📅 Church Event Calendar</h1>
            <p class="text-muted">View and manage upcoming church events.</p>
        </div>
    </div>

    <div class="row">
        <div class="col-md-3">
            <div class="sticky-top mb-3">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Draggable Events</h4>
                    </div>
                    <div class="card-body">
                        <!-- the events -->
                        <div id="external-events">
                            <div class="external-event bg-success">Sunday Service</div>
                            <div class="external-event bg-warning">Youth Meeting</div>
                            <div class="external-event bg-info">Choir Practice</div>
                            <div class="external-event bg-primary">Prayer Meeting</div>
                            <div class="external-event bg-danger">Special Event</div>
                            <div class="checkbox mt-3">
                                <label for="drop-remove">
                                    <input type="checkbox" id="drop-remove">
                                    remove after drop
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- /.card -->
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Create Event</h3>
                    </div>
                    <div class="card-body">
                        <div class="btn-group" style="width: 100%; margin-bottom: 10px;">
                            <ul class="fc-color-picker" id="color-chooser">
                                <li><a class="text-primary" href="#"><i class="fas fa-square"></i></a></li>
                                <li><a class="text-warning" href="#"><i class="fas fa-square"></i></a></li>
                                <li><a class="text-success" href="#"><i class="fas fa-square"></i></a></li>
                                <li><a class="text-danger" href="#"><i class="fas fa-square"></i></a></li>
                                <li><a class="text-muted" href="#"><i class="fas fa-square"></i></a></li>
                            </ul>
                        </div>
                        <div class="input-group">
                            <input id="new-event" type="text" class="form-control" placeholder="Event Title">
                            <div class="input-group-append">
                                <button id="add-new-event" type="button" class="btn btn-primary">Add</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- /.col -->
        <div class="col-md-9">
            <div class="card card-primary">
                <div class="card-body p-0">
                    <!-- THE CALENDAR -->
                    <div id="calendar"></div>
                </div>
                <!-- /.card-body -->
            </div>
            <!-- /.card -->
        </div>
        <!-- /.col -->
    </div>
    <!-- /.row -->
</div>

<!-- FullCalendar CSS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.css">

<!-- FullCalendar JS -->
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    var calendarEl = document.getElementById('calendar');

    var calendar = new FullCalendar.Calendar(calendarEl, {
        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: 'dayGridMonth,timeGridWeek,timeGridDay'
        },
        themeSystem: 'bootstrap',
        events: '{{ route("reports.calendar.events") }}',
        editable: true,
        droppable: true, // this allows things to be dropped onto the calendar
        drop: function(info) {
            // is the "remove after drop" checkbox checked?
            if (document.getElementById('drop-remove').checked) {
                // if so, remove the element from the "Draggable Events" list
                info.draggedEl.parentNode.removeChild(info.draggedEl);
            }
        },
        eventClick: function(info) {
            if (info.event.url) {
                window.location.href = info.event.url;
                info.jsEvent.preventDefault(); // prevents browser from following link in current tab.
            }
        }
    });

    calendar.render();

    /* ADDING EVENTS */
    var currColor = '#3c8dbc' //Red by default
    // Color chooser button
    $('#color-chooser > li > a').click(function(e) {
        e.preventDefault()
        // Save color
        currColor = $(this).css('color')
        // Add color effect to button
        $('#add-new-event').css({
            'background-color': currColor,
            'border-color': currColor
        })
    })
    $('#add-new-event').click(function(e) {
        e.preventDefault()
        // Get value and make sure it is not null
        var val = $('#new-event').val()
        if (val.length == 0) {
            return
        }

        // Create events
        var event = $('<div />')
        event.css({
            'background-color': currColor,
            'border-color': currColor,
            'color': '#fff'
        }).addClass('external-event')
        event.text(val)
        $('#external-events').prepend(event)

        // Add draggable funtionality
        ini_events(event)

        // Remove event from text input
        $('#new-event').val('')
    })
});
</script>

<style>
.external-event {
    box-shadow: 0 1px 3px rgba(0,0,0,.12),0 1px 2px rgba(0,0,0,.24);
    border-radius: 2px;
    cursor: move;
    font-weight: 700;
    margin-bottom: 4px;
    padding: 5px 10px;
    color: #fff;
}
#calendar {
    max-width: 100%;
    margin: 0 auto;
    padding: 10px;
}
</style>
@endsection
