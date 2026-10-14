@extends('layout.app')

@section('content')
    <style>
        .page-wrapper {
            max-width: 720px;
            margin: 0 auto;
            padding: 20px;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            display: flex;
            flex-direction: column;
            justify-content: center;
            /* align-items: center;  */
            min-height: 100vh;
        }


        /* Card container */
        .assessment-card {
            background: #fff;
            border-radius: 15px;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.12);
            overflow: hidden;
            margin-top: 30px;
            border: 1px solid #e0e0e0;
        }

        /* Card header */
        .assessment-card-header {
            color: #5f5e5e;
            font-weight: 900;
            font-size: 2rem;
            padding: 22px 30px;
            border-top-left-radius: 15px;
            border-top-right-radius: 15px;
            text-transform: capitalize;
            letter-spacing: 1.1px;
            text-align: center;
            user-select: none;
        }

        /* Each entry row */
        .assessment-entry {
            display: flex;
            gap: 25px;
            align-items: flex-start;
            padding: 20px 30px;
            border-bottom: 1px solid #eee;
        }

        .assessment-entry:last-child {
            border-bottom: none;
            padding-bottom: 30px;
        }

      
        .assessment-image {
            width: 160px;
            /* or auto */
            height: auto;
            /* keep ratio */
            max-width: 100%;
            /* responsive */
            object-fit: contain;
            /* show full image, no crop */
            border-radius: 8px;
            /* optional */
            background: #fff;
            /* optional if logo has transparent bg */
        }

        /* Text container */
        .assessment-text {
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        /* Title with underline */
        .assessment-title {
            font-weight: 900;
            font-size: 1.7rem;
            color: #2c3e50;
            margin-bottom: 10px;
            position: relative;
            padding-bottom: 8px;
            text-transform: capitalize;
        }

        .assessment-title::after {
            content: '';
            position: absolute;
            left: 0;
            bottom: 0;
            width: 70px;
            height: 5px;
            background: #27ae60;
            border-radius: 3px;
        }

        /* Description */
        .assessment-description {
            font-size: 1.05rem;
            color: #555;
            margin-bottom: 14px;
            line-height: 1.5;
            white-space: normal;
        }

        /* Extra info */
        .assessment-extra {
            font-size: 0.9rem;
            color: #888;
            font-style: italic;
        }

        /* Back button below card */
        .btn-primary {
            display: inline-block;
            margin: 35px auto 60px;
            padding: 12px 28px;
            font-size: 1.1rem;
            border-radius: 8px;
            /* background-color: #27ae60; */
            border: none;
            color: black;
            cursor: pointer;
            text-align: center;
            transition: background-color 0.3s ease;
        }


        div#assessmentCard {
            margin-top: 98px;
        }

        #assessmentCardHeaderWrapper {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 22px 30px;
            border-bottom: 1px solid #e0e0e0;
            background: #f9f9f9;
            text-transform: capitalize;
            font-weight: 900;
            font-size: 2rem;
            color: #2c3e50;
        }

        /* Download button styling */
        .downloadpdf {
            color: #2c3e50;
            background: transparent;
            border: none;
            cursor: pointer;
            font-size: 1.5rem;
            transition: color 0.3s ease;
        }

        .downloadpdf:hover {
            color: #cfece0;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .page-wrapper {
                max-width: 95%;
                margin: 20px auto;
            }

            .assessment-entry {
                flex-direction: column;
                align-items: center;
                text-align: center;
                padding: 20px 15px;
            }

            .assessment-image {
                width: 100%;
                max-width: 320px;
                height: auto;
                margin-bottom: 15px;
            }

            .assessment-text {
                align-items: center;
            }
        }
    </style>

    <div class="page-wrapper">
        <div class="assessment-card" id="assessmentCard">
            <div class="assessment-card-header" id="assessmentCardHeaderWrapper">
                <span id="assessmentCardHeader">Loading...</span>
                <a href="{{ route('homeadvice.download', $homeadvice_id) }}" class="btn btn-sm downloadpdf"
                    title="Download PDF">
                    <i class="fa-solid fa-download"></i>
                </a>
            </div>
            <div id="assessmentEntries">
               
            </div>
        </div>

       


        <a href="{{ route('homeadvice.index') }}" class="btn btn-primary">Back to List</a>
    </div>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        const IMAGE_PATH = "{{ url('/') }}/";
        $(document).ready(function () {
            var homeadviceId = {{ $homeadvice_id }};

            $.ajax({
                url: '/api/homeadvice/' + homeadviceId,
                method: 'GET',
                dataType: 'json',
                success: function (data) {
                    $('#assessmentCardHeader').text(
                        data.template_name ?
                            data.template_name.charAt(0).toUpperCase() + data.template_name.slice(1) :
                            'No Title'
                    );

                    let items = data.items ?? [];
                    let clinicLogo = data.clinic_logo; // ✅ get logo from API
                    let  IMAGE_PATH = "{{ asset('') }}";

                    if (items.length > 0) {
                        let html = '';

                        items.forEach(function (item) {
                            let imageUrl = '';
                          if (item.image) {
                            let cleanPath = item.image.replace(/^\/+/, '');
                            imageUrl = IMAGE_PATH + cleanPath;
                        } else {
                            imageUrl = clinicLogo;
                        }



                            html += `
                    <div class="assessment-entry">
                        <img src="${imageUrl}" alt="Assessment Image" 
                             class="assessment-image"
                             onerror="this.onerror=null;this.src='${clinicLogo}'">
                        <div class="assessment-text">
                            <div class="assessment-title">${item.title ? (item.title.charAt(0).toUpperCase() + item.title.slice(1)) : '-'}</div>
                            <div class="assessment-description">${item.description ? (item.description.charAt(0).toUpperCase() + item.description.slice(1)) : '-'}</div>
                        </div>
                    </div>
                `;
                        });

                        $('#assessmentEntries').html(html);
                    } else {
                        $('#assessmentEntries').html(
                            '<p style="padding: 20px; text-align: center;"><em>No homeadvice entries found.</em></p>'
                        );
                    }
                },
                error: function (xhr, status, error) {
                    $('#assessmentCardHeader').text('Error loading data');
                    $('#assessmentEntries').html(
                        '<p style="padding: 20px; text-align: center; color:red;">Failed to load home advice data.</p>'
                    );
                }
            });

        });
    </script>
@endsection