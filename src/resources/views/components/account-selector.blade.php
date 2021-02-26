<form action="{{ route('admin.change_account') }}" class="partner-selector-form" method="POST">
    @csrf
    <select class="partner-selector custom-select bg-primary w-100" name="account_id">
        @foreach (session('accounts') as $key => $account)
            <option value="{{ $key }}" {{ session('account_id') == $key ? 'selected' :''}}>{{ $account }}</option>
        @endforeach
    </select>
</form>
