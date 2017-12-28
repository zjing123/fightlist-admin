@foreach(['danger', 'warning', 'success', 'info'] as $msg)
    @if(session()->has($msg))
        <div class="alert alert-{{ $msg }}">
            <ul>
                <li>
                    {{ session()->get($msg) }}
                </li>
            </ul>
        </div>
    @endif
@endforeach