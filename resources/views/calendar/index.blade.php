<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Full Calendar js</title>
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.4.0/fullcalendar.css" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.18.1/moment.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.4.0/fullcalendar.min.js"></script>
    <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
</head>

<body>


    <!-- Modal -->
    <div class="modal fade" id="eventModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Create Event</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form method="POST" id="createEvent">
                        <label for="">Title: </label>
                        <input type="text" class="form-control" id="title" placeholder="Enter Title"><br>
                        <label for="">Start Date: </label>
                        <input type="date" class="form-control" id="start_date" placeholder="Start Date"><br>
                        <label for="">End Date: </label>
                        <input type="date" class="form-control" id="end_date" placeholder="End Date"><br>
                        <label for="">Select Color: </label>
                        <input type="color" class="form-control" id="color" placeholder=""><br>
                        <span id="titleError" class="text-danger"></span>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" id="saveBtn" class="btn btn-success">Save changes</button>
                </div>
                </form>
            </div>
        </div>
    </div>

    <div class="container">
        <div class="row">
            <div class="col-12">
                <h3 class="text-center mt-5">Laravel Full Calendar Integration</h3>
                <div class="col-md-11 offset-1 mt-5 mb-5">
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal"
                        data-bs-target="#eventModal">Create Event</button>
                    <div class="mt-5" id="calendar">

                    </div>

                </div>
            </div>
        </div>
    </div>


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.min.js"
        integrity="sha384-QJHtvGhmr9XOIpI6YVutG+2QOK9T+ZnN4kzFN1RtK3zEFEIsxhlmWl5/YESvpZ13" crossorigin="anonymous">
    </script>
    <script>
        $(document).ready(function() {
            var event = @json($events);
            $('#calendar').fullCalendar({
                header: {
                    'left': 'prev, next, today',
                    'center': 'title',
                    'right': 'month, agendaWeek, agendaDay',
                },
                events: event,
                selectable: true,
                selectHelper: true,
                function(start, end, allDays) {
                    // $('#eventModal').modal('toggle');

                    $('#createEvent').on('submit', function(e) {
                        e.preventDefault();
                        var title = $('#title').val();
                        var start_date = $('#start_date').val();
                        var end_date = $('#end_date').val();
                        var color = $('#color').val();

                        $.ajax({
                            url: "{{ route('calendar.store') }}",
                            type: "POST",
                            dataType: 'json',
                            data: {
                                _token: "{{ csrf_token() }}",
                                title,
                                start_date,
                                end_date,
                                color
                            },
                            success: function(response) {
                                $('#eventModal').modal('hide')
                                $('#calendar').fullCalendar('renderEvent', {
                                    'title': response.title,
                                    'start': response.start,
                                    'end': response.end,
                                    'color': response.color
                                });

                            },
                            error: function(error) {
                                if (error.responseJSON.errors) {
                                    $('#titleError').html(error.responseJSON.errors
                                        .title);
                                }
                            },
                        });
                    });
                },
                editable: true,
                eventDrop: function(event) {
                    var id = event.id;
                    var start_date = moment(event.start).format('YYYY-MM-DD');
                    var end_date = moment(event.end).format('YYYY-MM-DD');

                    $.ajax({
                        url: "{{ route('calendar.update', '') }}" + '/' + id,
                        type: "PATCH",
                        dataType: 'json',
                        data: {
                            _token: "{{ csrf_token() }}",
                            start_date,
                            end_date
                        },
                        success: function(response) {
                            swal("Good job!", "Event Updated!", "success");
                        },
                        error: function(error) {
                            console.log(error)
                        },
                    });
                },
                eventClick: function(event) {
                    var id = event.id;

                    if (confirm('Are you sure want to remove it')) {
                        $.ajax({
                            url: "{{ route('calendar.destroy', '') }}" + '/' + id,
                            type: "DELETE",
                            data: {
                                _token: "{{ csrf_token() }}",
                            },
                            dataType: 'json',
                            success: function(response) {
                                // swal("Good job!", "Event Deleted!", "success");
                                $('#calendar').fullCalendar('removeEvents', response);
                            },
                            error: function(error) {
                                console.log(error)
                            },
                        });
                    }

                },
                selectAllow: function(event) {
                    return moment(event.start).utcOffset(false).isSame(moment(event.end).subtract(1,
                        'second').utcOffset(false), 'day');
                },
            });

            $("#eventModal").on("hidden.bs.modal", function() {
                $('#saveBtn').unbind();
            });
        });
    </script>
</body>

</html>
