<div class="form-group">
    <label for="title">{{ __('Title') }}</label>
    <input type="text" id="title" name="title" class="form-control" value="{{ old('title', optional($post ?? null)->title) }}">
</div>
{{--  @error('title')
<div class="alert alert-danger">{{ $message }}</div>
@enderror  --}}
<div class="form-group">
    <label for="content">{{ __('Content') }}</label>
    <textarea class="form-control" id="content" name="content">{{ old('content', optional($post ?? null)->content) }}</textarea>
</div>

<div class="form-group">
    <label for="thumbnail">{{ __('Thumbnail') }}</label>
    <input type="file" id="thumbnail" name="thumbnail" class="form-control-file"/>
</div>

<x-errors>
</x-errors>