
<h2>Publications</h2>

<hr>

<div class="pagination-element-box-non-style">
    <form id="order-form" method="GET" action="{{ action('GroupController@groupPublications') }}">
        {{ csrf_field() }}
        <select name="field" form="order-form">
            <option value="created_at">Created At</option>
            <option value="updated_at">Updated At</option>
        </select>

        <select name="direction" form="order-form">
            <option value="asc">Ascendent</option>
            <option value="desc">Descendent</option>
        </select>
        
        <input type="hidden" name="order-form">
        <input type="submit" value="Order">
    </form>
</div>

<hr>

@if (count($publications) != 0)
<span class="link-pagination">
    {{ $publications->links() }}
</span>
@endif

@foreach ($publications as $publication)
<div class="pagination-element-box-style">
    <div class="pagination-content-wrapper">
        <p>Message From: {{ $publication->user->name }} {{ $publication->user->lastname }} ({{ $publication->user->email }})</p>
        <p>{{ $publication->text }}</p>
        @if ($publication->created_at != $publication->updated_at)
        <p style="text-align: right;">Updated at: {{ $publication->updated_at }}</p>
        @endif
        <p style="text-align: right;">Created at: {{ $publication->created_at }}</p>
    </div>
</div>

@endforeach

@if (count($publications) != 0)
<span class="link-pagination">
    {{ $publications->links() }}
</span>
@endif