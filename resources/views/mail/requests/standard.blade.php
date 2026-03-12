<x-mail::message>
<p style="text-align: center; margin: 0 0 18px;">
    <img src="https://escola.egp.ce.gov.br/assets/images/logo-egpce-original.png"
         alt="EGPCE"
         style="max-width: 280px; width: 100%; height: auto;">
</p>

# {{ $title }}

@if(!empty($intro))
{{ $intro }}
@endif

@if(!empty($details))
@foreach($details as $label => $value)
**{{ $label }}:** {{ $value }}

@endforeach
@endif

@if(!empty($listTitle))
**{{ $listTitle }}**
@endif

@if(!empty($listItems))
@foreach($listItems as $item)
- {{ $item }}
@endforeach
@endif

@if(!empty($actionUrl) && !empty($actionText))
<x-mail::button :url="$actionUrl">
{{ $actionText }}
</x-mail::button>
@endif

@if(!empty($footer))
{{ $footer }}
@endif

Atenciosamente,  
<b>Equipe EGPCE</b>
</x-mail::message>
