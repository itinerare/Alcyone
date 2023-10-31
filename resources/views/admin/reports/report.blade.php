@extends('admin.layout')

@section('admin-title') Report #{{ $report->id }} @endsection

@section('admin-content')
    {!! breadcrumbs(['Admin Panel' => 'admin', 'Report Queue' => 'admin/reports/pending', 'Report #'.$report->id => $report->adminUrl]) !!}

    <h1>
        Report #{{ $report->id }}
        <div class="float-end badge
            {{ $report->status == 'Pending' ? 'bg-primary' : '' }}
            {{ $report->status == 'Accepted' ? 'bg-warning' : '' }}
            {{ $report->status == 'Cancelled' ? 'bg-danger' : '' }}
        ">
                {{ $report->status }}
        </div>
    </h1>

    @include('reports._report_info', ['isAdmin' => true])

    @if($report->status == 'Pending')
        {!! Form::open(['url' => url()->current(), 'id' => 'reportForm']) !!}

        <div class="mb-3">
            {!! Form::label('staff_comments', 'Staff Comments', ['class' => 'form-label']) !!}
            {!! Form::textarea('staff_comments', $report->staff_comments, ['class' => 'form-control']) !!}
        </div>

        <div class="text-end">
            <a href="#" class="btn btn-danger me-2" data-bs-toggle="modal" data-bs-target="#confirmationModal" data-bs-action="ban">Ban Reporter</a>
            <a href="#" class="btn btn-info me-2" data-bs-toggle="modal" data-bs-target="#confirmationModal" data-bs-action="cancel">Cancel Report</a>
            <a href="#" class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#confirmationModal" data-bs-action="accept">Delete Image</a>
        </div>
        {!! Form::close() !!}
    @else
        <p>
            This report has already been processed.
        </p>

        <div class="card bg-body-secondary">
            <div class="card-body">
                <h3>Staff Comments</h3>
                @isset($report->staff_comments)
                    {!! $report->staff_comments !!}
                @else
                    <i>No staff comments provided.</i>
                @endisset
            </div>
        </div>
    @endif

    @if($report->status == 'Pending')
        <div class="modal fade" id="confirmationModal" tabindex="-1" role="dialog">
            <div class="modal-dialog" role="document">
                <!-- Accept -->
                <div class="modal-content hide" id="acceptContent">
                    <div class="modal-header">
                        <span class="modal-title h5 mb-0">Confirm Accept Report</span>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <p>
                            This will process the report as valid and delete the reported image. The image's uploader will be notified.
                        </p>
                        <div class="text-end">
                            <a href="#" id="acceptSubmit" class="btn btn-warning">Delete Image</a>
                        </div>
                    </div>
                </div>
                <!-- Cancel -->
                <div class="modal-content hide" id="cancelContent">
                    <div class="modal-header">
                        <span class="modal-title h5 mb-0">Confirm Cancel Report</span>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <p>
                            This will process the report as invalid. No other actions will be taken.
                        </p>
                        <div class="text-end">
                            <a href="#" id="cancelSubmit" class="btn btn-info">Cancel Report</a>
                        </div>
                    </div>
                </div>
                <!-- Ban -->
                <div class="modal-content hide" id="banContent">
                    <div class="modal-header">
                        <span class="modal-title h5 mb-0">Confirm Ban</span>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <p>
                            This will cancel this report and ban the reporter, preventing them from reporting any future images. Any other reports made by this reporter that are currently pending will be cancelled.
                        </p>
                        <div class="text-end">
                            <a href="#" id="banSubmit" class="btn btn-danger">Ban Reporter</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
@endsection

@section('scripts')
    @parent
    @if ($report->status == 'Pending')
        <script type="module">
            const $reportForm = $('#reportForm');

            const $acceptContent = $('#acceptContent');
            const $acceptSubmit = $('#acceptSubmit');

            const $cancelContent = $('#cancelContent');
            const $cancelSubmit = $('#cancelSubmit');

            const $banContent = $('#banContent');
            const $banSubmit = $('#banSubmit');

            const confirmationModal = document.getElementById('confirmationModal')
            if (confirmationModal) {
                confirmationModal.addEventListener('show.bs.modal', event => {
                    const button = event.relatedTarget;
                    const action = button.getAttribute('data-bs-action');

                    switch (action) {
                        case 'accept':
                            $acceptContent.removeClass('d-none');
                            $cancelContent.addClass('d-none');
                            $banContent.addClass('d-none');
                            break;
                        case 'cancel':
                            $cancelContent.removeClass('d-none');
                            $acceptContent.addClass('d-none');
                            $banContent.addClass('d-none');
                            break;
                        case 'ban':
                            $banContent.removeClass('d-none');
                            $cancelContent.addClass('d-none');
                            $acceptContent.addClass('d-none');
                            break;
                    }
                })
            }

            $(document).ready(function() {
                $acceptSubmit.on('click', function(e) {
                    e.preventDefault();
                    $reportForm.attr('action', '{{ url()->current() }}/accept');
                    $reportForm.submit();
                });

                $cancelSubmit.on('click', function(e) {
                    e.preventDefault();
                    $reportForm.attr('action', '{{ url()->current() }}/cancel');
                    $reportForm.submit();
                });

                $banSubmit.on('click', function(e) {
                    e.preventDefault();
                    $reportForm.attr('action', '{{ url()->current() }}/ban');
                    $reportForm.submit();
                });
            });
        </script>
    @endif
@endsection
