{{--
    task #121: the page's return_to / return_label as hidden inputs, so a
    save or a Deactivate/Activate lands back on the edit page with the same
    back link. $returnToParams is ResolvesReturnTo::returnToParams(): only
    what the page received and what passed its checks (after a validation
    error, the submitted values, checked the same way), so these always
    match the page's back link. The Tasks fallback is never sent as an
    explicit value. The controller checks them again on submit.
--}}
@isset($returnToParams['return_to'])
    <input type="hidden" name="return_to" value="{{ $returnToParams['return_to'] }}">
@endisset
@isset($returnToParams['return_label'])
    <input type="hidden" name="return_label" value="{{ $returnToParams['return_label'] }}">
@endisset
