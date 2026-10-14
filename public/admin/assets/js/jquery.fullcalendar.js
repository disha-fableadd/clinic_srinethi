!function ($) {
    "use strict";

    var CalendarApp = function () {
        this.$modal = $(` 
            <div id="event-modal" class="modal fade" tabindex="-1" role="dialog">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title"></h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body"></div>
                        <div class="modal-footer">
                      <button type="button" class="btn btn-primary view-appointment" data-id="{{ appointment.id }}">View</button>

                            <button type="button" class="btn btn-danger btn-rounded" data-dismiss="modal">Close</button>
                        </div>
                    </div>
                </div>
            </div>
        `).appendTo("body");

        this.$calendar = $('#calendar, #mycalendar');
        this.$calendarObj = null;
        this.currentFilter = "all"; // Default filter to show all appointments
    };

    /* Handle event click */
    CalendarApp.prototype.onEventClick = function (calEvent) {
        var $this = this;

        $this.$modal.find('.modal-title').html(
            `${calEvent.title} <span> Appointment</span>`
        );


        // $this.$modal.find('.modal-body').html(
        //     `<p><strong>Patient :  </strong><strong>${calEvent.patient_name}</strong> has an appointment for <strong>${calEvent.treatment_name}</strong> with <strong>Dr. ${calEvent.doctor_name}</strong>.</p>` +
        //     `<p><strong>Date : </strong> ${calEvent.start.format("YYYY-MM-DD")}</p>` +

        //     (calEvent.end ? `<p><strong>End:</strong> ${calEvent.end.format("YYYY-MM-DD")}</p>` : "")
        // );
        //     $this.$modal.find('.view-appointment').attr('data-id', calEvent.id);
        //     $this.$modal.modal('show');
        // };
        function ucfirst(str) {
            return str.charAt(0).toUpperCase() + str.slice(1);
        }

        $this.$modal.find('.modal-body').html(
            `<p><strong>Patient :  </strong><strong>${ucfirst(calEvent.patient_name)}</strong> has an appointment for <strong>${ucfirst(calEvent.treatment_name)}</strong> with <strong>Dr. ${ucfirst(calEvent.doctor_name)}</strong>.</p>` +
            `<p><strong>Date : </strong> ${calEvent.start.format("YYYY-MM-DD")}</p>` +
            (calEvent.end ? `<p><strong>End:</strong> ${calEvent.end.format("YYYY-MM-DD")}</p>` : "")
        );
        $this.$modal.find('.view-appointment').attr('data-id', calEvent.id);
        $this.$modal.modal('show');
    };

    /* Fetch events */
    CalendarApp.prototype.fetchEvents = function (start, end, timezone, callback) {
        let status = $('#filter-event-state').val();
        let filter = this.currentFilter == "All" ? "all" : this.currentFilter;
        let branchId = localStorage.getItem('selectedBranchId');

        $.ajax({
            url: "/api/calendar",
            method: "GET",
            data: { status: status, filter: filter, branch_id: branchId },
            success: function (response) {
                callback(response); // Send event data to FullCalendar
            },
            error: function () {
                console.log("Error fetching appointments.");
            }
        });
    };

    /* Initialize Calendar */
    CalendarApp.prototype.init = function () {
        var $this = this;

        if (!$this.$calendar.length) {
            console.log("Calendar element not found!");
            return;
        }

        // Ensure the default filter is "All" (empty string means no filter in the API request)
        $this.currentFilter = "";

        // Initialize FullCalendar
        $this.$calendarObj = $this.$calendar.fullCalendar({
            defaultView: "month",
            height: $(window).height() - 200,
            header: {
                left: "prev,next today",
                center: "title",
                right: "month,agendaWeek,agendaDay"
            },
            events: function (start, end, timezone, callback) {
                $this.fetchEvents(start, end, timezone, callback);
            },
            eventClick: function (calEvent) {
                $this.onEventClick(calEvent);
            }
        });


        setTimeout(function () {
            $this.$calendarObj.fullCalendar('refetchEvents');
        }, 500);


        $('#filter-event-type button').click(function () {
            $this.currentFilter = "Today";
            $this.$calendarObj.fullCalendar('refetchEvents');
        });


        $('#filter-event-state').change(function () {
            let selectedStatus = $(this).val();
            if (selectedStatus == "All") {
                $this.currentFilter = "All"; // Explicitly set to "All"
            }

            $this.$calendarObj.fullCalendar('refetchEvents');
        });
    };



    $(document).ready(function () {
        $.CalendarApp = new CalendarApp();
        $.CalendarApp.Constructor = CalendarApp;
        $.CalendarApp.init();
    });

}(window.jQuery);

$(document).on('click', '.view-appointment', function () {
    var appointmentId = $(this).data('id');
    window.location.href = '/appointment/show/' + appointmentId;
});

