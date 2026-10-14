@extends('layout.app')
<link rel="stylesheet" href="{{ asset(env('IMAGE_PATH').'admin/assets/css/dietchart-show.css') }}"> 
@section('content')
    

    <div class="page-wrapper">
        <div class="dietcard-card" id="dietCard">
            <div class="dietcard-card-header" id="dietCardHeaderWrapper">
                <span id="dietCardHeader">Loading...</span>
                <a href="{{ route('dietchart.download', $dietchart_id) }}" class="btn btn-sm downloadpdf" title="Download PDF">
                    <i class="fa-solid fa-download"></i>
                </a>
            </div>
            <div id="dietEntries">
                <!-- AJAX-loaded entries will go here -->
            </div>
        </div>

        <a href="{{ route('dietchart.index') }}" class="btn btn-primary">Back to List</a>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <script>
        $(document).ready(function() {
            var dietChartId = {{ $dietchart_id }};

            $.ajax({
                url: '/api/dietchart/' + dietChartId,
                method: 'GET',
                dataType: 'json',
                success: function(data) {
                    var clinicLogo = data.clinic_logo;

                    $('#dietCardHeader').text(data.name ? data.name.charAt(0).toUpperCase() + data.name
                        .slice(1) : 'No Title');

                    var titles = [];
                    var descriptions = [];
                    var images = [];
                    var times = [];

                    try {
                        titles = Array.isArray(data.title) ? data.title : JSON.parse(data.title ||
                        '[]');
                    } catch (e) {}
                    try {
                        descriptions = Array.isArray(data.description) ? data.description : JSON.parse(
                            data.description || '[]');
                    } catch (e) {}
                    try {
                        images = Array.isArray(data.image) ? data.image : JSON.parse(data.image ||
                        '[]');
                    } catch (e) {}
                    try {
                        times = Array.isArray(data.time) ? data.time : JSON.parse(data.time || '[]');
                    } catch (e) {}

                    if (titles.length === 0) {
                        $('#dietEntries').html(
                            '<p style="padding:20px; text-align:center;"><em>No diet entries found.</em></p>'
                            );
                        return;
                    }

                    var html = '';
                    for (var i = 0; i < titles.length; i++) {
                        html += '<div class="dietcard-entry">';
                        var imgPath = images[i] ? '{{ url('/') }}/public/' + images[i].replace(
                            /^\/?/, '') : clinicLogo;

                        html +=
                            `<img src="${imgPath}" alt="Diet Image" class="dietcard-image" onerror="this.onerror=null;this.src='${clinicLogo}'">`;

                        html += '<div class="dietcard-text">';
                        html += '<div class="dietcard-title">' + (titles[i] ? titles[i].charAt(0)
                            .toUpperCase() + titles[i].slice(1) : '-') + '</div>';
                        html += '<div class="dietcard-description">' + (descriptions[i] ? descriptions[
                            i].charAt(0).toUpperCase() + descriptions[i].slice(1) : '-') + '</div>';
                        html += '<div class="dietcard-time">' + (times[i] ? times[i] : '-') + '</div>';
                        html += '</div>';

                        html += '</div>';
                    }

                    $('#dietEntries').html(html);
                },
                error: function(xhr, status, error) {
                    $('#dietCardHeader').text('Error loading data');
                    $('#dietEntries').html(
                        '<p style="padding:20px; text-align:center; color:red;">Failed to load diet chart data.</p>'
                        );
                }
            });

        });
    </script>
@endsection
