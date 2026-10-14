@extends('layout.app')

@section('content')

   <link rel="stylesheet" href="{{ asset(env('IMAGE_PATH').'admin/assets/css/assessment-show.css') }}"> 


    <div class="page-wrapper">
        <div class="assessment-card" id="assessmentCard">
            <div class="assessment-card-header" id="assessmentCardHeaderWrapper">
                <span id="assessmentCardHeader">Loading...</span>
                <a href="{{ route('assessment.download', $assessment_id) }}" class="btn btn-sm downloadpdf"
                    title="Download PDF">
                    <i class="fa-solid fa-download"></i>
                </a>
            </div>
            <div id="assessmentEntries">
                <!-- AJAX-loaded entries will go here -->
            </div>
        </div>

        <a href="{{ route('assessment.index') }}" class="btn btn-primary">Back to List</a>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  

    <script>
        $(document).ready(function () {
            var assessmentId = {{ $assessment_id }};

            $.ajax({
                url: '/api/assessment/' + assessmentId,
                method: 'GET',
                dataType: 'json',
                success: function (data) {

                    var clinicLogo = data.clinic_logo;
                    var assessment = data.assessment;

                    $('#assessmentCardHeader').text(data.name ? data.name.charAt(0).toUpperCase() + data
                        .name.slice(1) : 'No Title');

                    var titles = [];
                    var descriptions = [];
                    var images = [];

                    try {
                        titles = Array.isArray(data.title) ? data.title : JSON.parse(data.title ||
                            '[]');
                    } catch (e) { }

                    try {
                        descriptions = Array.isArray(data.description) ? data.description : JSON.parse(
                            data.description || '[]');
                    } catch (e) { }

                    try {
                        images = Array.isArray(data.image) ? data.image : JSON.parse(data.image ||
                            '[]');
                    } catch (e) { }

                    if (titles.length === 0) {
                        $('#assessmentEntries').html(
                            '<p style="padding:20px; text-align:center;"><em>No assessment entries found.</em></p>'
                        );
                        return;
                    }

                    var html = '';
                    for (var i = 0; i < titles.length; i++) {
                        html += '<div class="assessment-entry">';

                        var imgPath = images[i]
    ? "{{ asset('') }}" + images[i].replace(/^\/+/, '')
    : clinicLogo;


                            console.log(imgPath);
                            
                        html += `<img src="${imgPath}" 
          alt="Assessment Image" 
          class="assessment-image"
          onerror="this.onerror=null;this.src='${clinicLogo}'">`;
                        // Text
                        html += '<div class="assessment-text">';
                        html += '<div class="assessment-title">' + (titles[i] ? titles[i].charAt(0)
                            .toUpperCase() + titles[i].slice(1) : '-') + '</div>';
                        html += '<div class="assessment-description">' + (descriptions[i] ?
                            descriptions[i].charAt(0).toUpperCase() + descriptions[i].slice(1) : '-'
                        ) + '</div>';
                        html += '</div>';

                        html += '</div>';
                    }

                    $('#assessmentEntries').html(html);
                },
                error: function (xhr, status, error) {
                    $('#assessmentCardHeader').text('Error loading data');
                    $('#assessmentEntries').html(
                        '<p style="padding:20px; text-align:center; color:red;">Failed to load assessment data.</p>'
                    );
                }
            });
        });
    </script>
@endsection