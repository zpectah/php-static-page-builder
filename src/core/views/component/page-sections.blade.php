@foreach($content['section'] as $section)
    <section
        class="section section--sections"
        id="section_{{$section['key']}}"
    >
        <h2>
            {{$section['title']}}
        </h2>
        <div>
            {!! $section['html'] !!}
        </div>
    </section>
@endforeach