@props(['name', 'label', 'accept' => null])
<div class='form-field'>
    <label>{{ $label }}</label><label class='file-uploader' data-file-uploader><input type='file'
            name='{{ $name }}' @if ($accept) accept='{{ $accept }}' @endif><span
            class='upload-icon'>⇧</span><strong>Pilih atau jatuhkan file</strong><small data-file-name>Belum ada file
            dipilih</small></label>
    @error($name)
        <div class='text-danger small'>{{ $message }}</div>
    @enderror
</div>
