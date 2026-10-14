@extends('layout.app')
<style>
    .appointment-title {

        text-align: left !important;
    }

    .page-title {
        padding-top: 5px !important;
        font-size: 20px !important;

    }

    .form-container {
        margin-top: 10 !important;
    }

    #results .card-body {
        padding: 20px 20px;
    }

    .knowledge-form {
        height: auto !important;
    }

    .btn {
        padding: 7.504px 12px !important;
    }

    /* #recentSearches {
        background: #f9f9f9;
        border-radius: 12px;
        padding: 20px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06);
        
    } */

    #recentSearchList {
        display: flex;
        flex-direction: column;
        gap: 10px;
    }

    .search-row {
        display: flex;
        flex-wrap: nowrap;
        gap: 10px;
    }

    .recent-search-item {
        flex: 1;
        max-width: 32%;
    }

    @media (max-width: 767px) {
        .recent-search-item {
            max-width: 100%;
        }
    }


    .recent-title {
        font-size: 18px;
        font-weight: 600;
        color: #333;
        margin-bottom: 15px;
        border-left: 4px solid #cfece0;
        padding-left: 10px;
    }

    .recent-search-item {
        list-style: none;
        padding: 0;
        margin: 0;
    }

    .recent-search-item .list-group-item {
        background-color: #fff;
        border: 1px solid #e3e3e3;
        margin-bottom: 8px;
        padding: 10px 15px;
        border-radius: 8px;
        transition: all 0.3s ease;
        font-size: 15px;
        color: #444;
        font-weight: 500;
    }

    .recent-search-item .list-group-item:hover {
        background-color: #cfece0;
        color: black;
        transform: translateX(5px);
        cursor: pointer;
        box-shadow: 0 4px 12px #cfece0;
    }

    .recent-search-item .list-group-item::before {
        content: "🔍";
        margin-right: 8px;
    }

    .delete-search i {
        font-size: 14px;
        transition: color 0.3s;
    }

    .delete-search:hover i {
        color: #dc3545;
        /* Bootstrap danger red */
    }

    .search-text {
        flex-grow: 1;
    }

    #allSearchList .list-group-item {
        font-size: 16px;
        padding: 10px 15px;
        border: 1px solid #ddd;
    }

    #addSymptomModal .search-text {
        font-weight: 500;
        color: #333;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        max-width: 100%;
    }

    .modal-body {
        padding: 0 15px 15px 15px !important;
    }

    .modal-body .col-md-4 {
        padding: 15px 15px 0 15px !important;
    }

    #allSearchList .border:hover {
        background-color: #e6f4f1;
        transition: 0.2s ease;
    }
</style>
@section('content')
    <div class="page-wrapper">
        <div class="content">
            <div class="row" style="padding-top:15px">
                <div class="col-12">
                    <h4 class="page-title appointment-title "><i class="fas fa-book"></i> Knowledge Base</h4>
                </div>
            </div>
            <div class="row">
                <div class="col-12">
                    <div class="form-container all-form knowledge-form" id="appointmentForm">
                        <div class="row">
                            <div class="col-md-9">
                                <div class="input-group mb-3">

                                    <input type="text" id="searchQuery" class="form-control"
                                        placeholder="Enter medical topic (e.g., fever symptoms)">
                                </div>
                            </div>
                            <div class="col-md-3 d-flex align-items-start">
                                <button id="searchBtn" class="btn btn-primary w-100">
                                    <i class="fas fa-search"></i> <span class="hdr-btn-text">Search</span>
                                </button>
                            </div>
                        </div>
                        <div id="recentSearches" class="mb-4">
                            <div class="d-flex justify-content-between align-items-center">
                                <h5 class="recent-title mb-0"><i class="fas fa-history me-2"></i> Recent Searches</h5>
                                <button class="btn btn-link  btn-primary btn-rounded" data-bs-toggle="modal"
                                    data-bs-target="#addSymptomModal" style="text-decoration: none; margin-top:10px">
                                    View All
                                </button>

                            </div>
                            <ul id="recentSearchList" class="list-group recent-search-list mt-2"></ul>
                        </div>



                        <div id="results"></div>
                    </div>
                </div>
            </div>


        </div>
    </div>
    <!-- Modal -->
    <div class="modal fade" id="addSymptomModal" tabindex="-1" aria-labelledby="addSymptomModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header" style="background-color: #CFECE0; color:black">
                    <h5 class="modal-title d-flex align-items-center" id="addSymptomModalLabel">
                        <i class="fas fa-history me-2" style="font-size: 18px;padding-right:5px"></i> All Recent Searches
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"
                        style="cursor:pointer">X</button>
                </div>

                <div class="modal-body">
                    <div id="allSearchList" class="row g-3">
                        <!-- All search history items will be injected here dynamically -->
                    </div>
                </div>


            </div>
        </div>
    </div>





    <link rel="stylesheet" href="https://cdn.datatables.net/2.2.1/css/dataTables.dataTables.css">
    <script src=" https://code.jquery.com/jquery-3.7.1.js"></script>
    <script src="https://cdn.datatables.net/2.2.1/js/dataTables.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <!-- Select2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <!-- SweetAlert2 CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <!-- SweetAlert2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.js"></script>


    <script>
        const MAX_RECENT = 6;

        function getRecentSearches() {
            const stored = localStorage.getItem('recentSearches');
            return stored ? JSON.parse(stored) : [];
        }

    
        function saveRecentSearch(query) {
            let searches = getRecentSearches();

            // Remove duplicates and put newest first
            searches = [query, ...searches.filter(q => q !== query)];

            localStorage.setItem('recentSearches', JSON.stringify(searches));
        }




        function renderRecentSearches() {
            const recentSearches = getRecentSearches();
            const list = $('#recentSearchList');
            list.html('');

            if (recentSearches.length === 0) {
                $('#recentSearches').show();
                list.html(`<li class="list-group-item text-muted">No search history found.</li>`);
                return;
            }

            const limitedSearches = recentSearches.slice(0, MAX_RECENT);


            // Create row structure for 3 items per row
            let row;
            limitedSearches.forEach((query, index) => {
                if (index % 3 === 0) {
                    row = $('<div class="search-row d-flex flex-wrap mb-2"></div>');
                    list.append(row);
                }

                const item = $(`
                                    <div class="recent-search-item flex-fill me-2 mb-2" style="min-width: 30%;">
                                        <div class="list-group-item d-flex justify-content-between align-items-center" style="cursor:pointer;">
                                            <span class="search-text">${query}</span>
                                            <span class="delete-search text-danger" data-index="${index}" data-context="main">
                                                <i class="fas fa-trash-alt"></i>
                                            </span>
                                        </div>
                                    </div>
                                `);
                row.append(item);
            });

            $('#recentSearches').show();
        }


        function performSearch(query = null) {
            if (!query) query = $('#searchQuery').val().trim();
            if (!query) {
                Swal.fire("Please enter a query.");
                return;
            }

            $('#searchQuery').val(query); // Set back the search box
            saveRecentSearch(query); // Save to recent list
            $('#results').html('<p>Searching...</p>');

            $.get("/admin/search-google", { q: query }, function (data) {
                if (data.items) {
                    let html = '';
                    data.items.forEach(item => {
                        html += `
                                    <div class="card mb-2">
                                        <div class="card-body">
                                            <h5><a href="${item.link}" target="_blank">${item.title}</a></h5>
                                            <p>${item.snippet}</p>
                                        </div>
                                    </div>`;
                    });
                    $('#results').html(html);
                } else {
                    $('#results').html('<p>No results found.</p>');
                }
                renderRecentSearches();
            });
        }

        // Events
        $('#searchBtn').on('click', function () {
            performSearch();
        });

        function populateAllSearchModal() {
            const recentSearches = getRecentSearches();
            const list = $('#allSearchList');
            list.html('');

            if (recentSearches.length === 0) {
                list.html('<div class="text-muted">No search history found.</div>');
                return;
            }

            recentSearches.forEach((query, index) => {
                const card = $(`
                                    <div class="col-md-4">
                                        <div class="border rounded p-2 d-flex justify-content-between align-items-center bg-white shadow-sm" style="min-height: 50px; cursor: pointer;">
                                            <span class="d-flex align-items-center">
                                                <i class="fas fa-search me-2 text-muted" style="margin-right: 5px;"></i>
                                                <span class="search-text">${query}</span>
                                            </span>
                                            <span class="text-danger delete-search" data-index="${index}" data-context="modal" style="cursor: pointer;">
                                                <i class="fas fa-trash-alt"></i>
                                            </span>
                                        </div>
                                    </div>
                                `);
                list.append(card);
            });
        }


        // Re-bind it to the new modal
        $('#addSymptomModal').on('show.bs.modal', function () {
            populateAllSearchModal();
        });



        $('#searchQuery').on('keydown', function (e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                performSearch();
            }
        });

        // Handle click on recent search item
        $(document).on('click', '.recent-search-item', function () {
            const query = $(this).find('.search-text').text().trim();
            performSearch(query);
        });
        // Handle click on modal search item
        $(document).on('click', '#allSearchList .border', function () {
            const query = $(this).find('.search-text').text().trim();
            performSearch(query);
            $('#addSymptomModal').modal('hide'); // Optional: close modal after selecting
        });


        // On load
        $(document).ready(function () {
            renderRecentSearches();
        });
        // Handle delete icon click
        $(document).on('click', '.delete-search', function (e) {
            e.stopPropagation();
            const index = $(this).data('index');
            const context = $(this).data('context'); // main or modal

            let searches = getRecentSearches();
            searches.splice(index, 1);
            localStorage.setItem('recentSearches', JSON.stringify(searches));

            renderRecentSearches(); // always update main section
            if (context === 'modal') {
                populateAllSearchModal(); // refresh modal if delete from modal
            }
        });

    </script>






@endsection