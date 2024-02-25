@if ($rank)
    {{ html()->modelForm($rank)->open() }}

    <div class="mb-3">
        {{ html()->label('Rank Name', 'name')->class('form-label') }}
        {{ html()->text('name')->class('form-control') }}
    </div>

    <div class="mb-3">
        {{ html()->label('Description (Optional)', 'description')->class('form-label') }}
        {{ html()->textarea('description')->class('form-control') }}
    </div>

    <div class="text-end">
        {{ html()->submit($rank->id ? 'Edit' : 'Create')->class('btn btn-primary') }}
    </div>

    {{ html()->closeModelForm() }}
@else
    Invalid rank selected.
@endif
