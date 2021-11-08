<img wire:poll.visible.2s wire:model="QrImage" class="qrImage mb-3 mt-3" src="{{ $data->url }}" alt="qr" data-area="{{ $data->area }}">

@if($data->area == 1 )
<script>
     document.querySelector('.btnNext:not(.btnPrev):not(.finish)').click(); 
</script>
@endif