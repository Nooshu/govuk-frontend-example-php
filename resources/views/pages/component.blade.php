@component('layouts.govuk', ['pageTitle' => $pageTitle, 'beforeContent' => $beforeContent ?? null])
  <h1 class="govuk-heading-l">{{ $info['title'] }}</h1>
  <p class="govuk-body">
    Preview of the PHP renderer for this component. Choose a version below to compare the PHP HTML with the official fixture from GOV.UK Frontend {{ $frontendVersion }}.
  </p>
  <p class="govuk-body">
    <a class="govuk-link" href="{{ $info['designSystemUrl'] }}">{{ $info['title'] }} on the GOV.UK Design System</a>
  </p>
  <h2 class="govuk-heading-m">Current version: {{ $fixtureName }}</h2>
  {!! $parityHtml !!}
  @if($fixtureDescription !== '')
    {!! \App\Govuk\Renderer::render('inset-text', ['text' => $fixtureDescription]) !!}
  @endif
  <div class="app-component-preview">
    <h3 class="govuk-heading-s">Component preview</h3>
    <div class="app-component-preview__frame">
      {!! $rendered !!}
    </div>
  </div>
  <h2 class="govuk-heading-m">Versions (Fixtures)</h2>
  <nav aria-label="Versions">
    <ul class="govuk-list">
      @foreach($versions as $version)
        <li>
          @if($version['current'])
            <strong aria-current="true">{{ $version['name'] }}</strong>
            {!! \App\Govuk\Renderer::render('tag', ['text' => 'Current']) !!}
          @else
            <a class="govuk-link" href="/components/{{ $info['name'] }}?fixture={{ urlencode($version['name']) }}">{{ $version['name'] }}</a>
          @endif
        </li>
      @endforeach
    </ul>
  </nav>
@endcomponent
