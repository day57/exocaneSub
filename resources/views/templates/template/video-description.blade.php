@section('site_title', formatTitle([__('Video description'), __('Template'), config('settings.title')]))

@section('head_content')

@endsection

@include('shared.breadcrumbs', ['breadcrumbs' => [
    ['url' => route('dashboard'), 'title' => __('Home')],
    ['url' => route('templates'), 'title' => __('Templates')],
    ['title' => __('Template')],
]])

<div class="d-flex">
    <h1 class="h2 mb-0 text-break">{{ __('Video description') }}</h1>
</div>

<div class="row mx-n2">
    <div class="col-12 col-lg-5 px-2">
        <div class="card border-0 shadow-sm mt-3 @if(isset($documents)) d-none d-lg-flex @endif" id="ai-form">
            <div class="card-header align-items-center">
                <div class="row">
                    <div class="col">
                        <div class="font-weight-medium py-1">{{ __($template->name) }}</div>
                    </div>
                </div>
            </div>
            <div class="card-body position-relative">
                @include('shared.message')

                <form action="{{ route('templates.video_description') }}" method="post" enctype="multipart/form-data" @cannot('templates', ['App\Models\User']) class="position-relative opacity-20" @endcannot>
                    @cannot('templates', ['App\Models\User'])
                        <div class="position-absolute top-0 right-0 bottom-0 left-0 z-1 bg-fade-0"></div>
                    @endcannot

                    @csrf

                    <input type="hidden" name="template_id" value="{{ $template->id }}">

                    <div class="form-group">
                        <label for="i-name">{{ __('Name') }}</label>
                        <input type="text" name="name" id="i-name" class="form-control{{ $errors->has('name') ? ' is-invalid' : '' }}" value="{{ $name ?? (old('name') ?? '') }}">
                        @if ($errors->has('name'))
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $errors->first('name') }}</strong>
                            </span>
                        @endif
                        <small class="form-text text-muted">{{ __('The name of the document.') }}</small>
                    </div>

                    <div class="form-group">
                        <label for="i-description">{{ __('Description') }}</label>
                        <textarea rows="3" data-auto-resize-textarea="true" name="description" id="i-description" class="form-control{{ $errors->has('description') ? ' is-invalid' : '' }}" placeholder="{{ __('Learn how to become a programmer in 5 easy steps') }}">{{ $description ?? (old('description') ?? '') }}</textarea>
                        @if ($errors->has('description'))
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $errors->first('description') }}</strong>
                            </span>
                        @endif
                        <small class="form-text text-muted">{{ __('The only limit is your imagination.') }}</small>
                    </div>

                    @include('templates.partials.common-inputs')

                    <div class="row mx-n2">
                        <div class="col px-2">
                            <button type="submit" name="submit" class="btn btn-primary position-relative" data-button-loader>
                                <div class="position-absolute top-0 right-0 bottom-0 left-0 d-flex align-items-center justify-content-center">
                                    <span class="d-none spinner-border spinner-border-sm width-4 height-4" role="status"></span>
                                </div>
                                <span class="spinner-text">{{ __('Generate') }}</span>&#8203;
                            </button>
                        </div>
                        <div class="col-auto px-2">
                            <a href="{{ route('templates.video_description') }}" class="btn btn-outline-secondary d-none {{ (__('lang_dir') == 'rtl' ? 'mr-auto' : 'ml-auto') }}">{{ __('Reset') }}</a>
                            <button class="btn btn-outline-secondary {{ (__('lang_dir') == 'rtl' ? 'mr-auto' : 'ml-auto') }}" type="button" data-toggle="collapse" data-target="#collapse-form-advanced" aria-expanded="{{ $errors->has('language') || $errors->has('creativity') || $errors->has('variations') ? 'true' : 'false'}}" aria-controls="collapse-form-advanced">
                                {{ __('Advanced') }}
                            </button>
                        </div>
                    </div>
                </form>

                @cannot('templates', ['App\Models\User'])
                    <div class="position-absolute top-0 right-5 bottom-0 left-5">
                        @if(paymentProcessors())
                            @include('shared.features.locked')
                        @else
                            @include('shared.features.unavailable')
                        @endif
                    </div>
                @endcannot
            </div>
        </div>

        @if(isset($documents))
            <a href="#" class="btn btn-outline-secondary btn-block d-lg-none mt-3" id="ai-form-show-button">{{ __('Show form') }}</a>
        @endif
    </div>

    <div class="col-12 col-lg-7 px-2">
        @if(isset($documents))
            <div class="mt-3" id="ai-results">
                @foreach($documents as $document)
                    <div class="mt-3">
                        @include('templates.partials.document-result', ['document' => $document])
                    </div>
                @endforeach
            </div>
        @endif

        <div class="position-relative pt-3 h-100 @if(isset($documents)) d-none @else d-flex @endif" id="ai-placeholder-results">
            <div class="position-relative h-100 align-items-center justify-content-center d-flex w-100">
                <div class="text-muted font-weight-medium z-1" id="ai-placeholder-text-start">
                    <div class="width-6 height-6 mt-5"></div>
                    <div class="my-3">{{ __('Start by filling the form.') }}</div>
                    <div class="width-6 height-6 mb-5"></div>
                </div>
                <div class="text-muted flex-column font-weight-medium z-1 align-items-center d-none " id="ai-placeholder-text-progress">
                    <div class="width-6 height-6 mt-5"></div>
                    <div class="my-3">{{ __('Generating the content, please wait.') }}</div>
                    <div class="spinner-border spinner-border-sm width-6 height-6 mb-5" role="status"></div>
                </div>
                <div class="position-absolute top-0 right-0 bottom-0 left-0 border rounded border-width-2 border-dashed opacity-20 z-0"></div>
            </div>
        </div>
    </div>
</div>
