# DLH Combobox

## 1 Description
The DLH Combobox is an enhanced form control, that is used to select an entry out of larger data-sets.

It is intented to be used, when a regular select or radio buttons do not fit anymore.

The combobox Element consists of:

  1. an input of type text, that is used to input a text.

  2. a select control which pops up as a list to show search results. It's size is always >= 2, to force browsers showing it as a list.

  3. a hidden input field, that holds the selected data.

  4. optional html containers below the input, that can be used to show entries for selection, that may be most recent or most relevant entries. This should make it easier for users, to find often used values, and reduces the need to search for entries.

## 2 Usage

It can be used with html like this:

```
<div class="mb-3 dlh-combobox" id="projectSearchContainer">
    <label for="projectSearch" class="form-label">Projekt: </label>
    <input type="text" class="form-control dlh-combobox-search" placeholder="Suche nach einem Projekt" value="Aktueller Text"/>
    <select class="form-select dlh-combobox-result" size="3" aria-label="size select example" style="display: none;">
        <option>Keine Daten</option>
    </select>
    <input type="hidden" name="projectId" class="dlh-combobox-value" value="ID"/>
</div>
```

The combox must be initialized by javascript like this:

```
window.addEventListener('DOMContentLoaded', (event) => {
    dlhProjectComboBox = new DlhComboBoxControl('#projectSearchContainer');
});
```

The Javascript file for the combobox has to bee included:

```<script src="{{ asset('js/Service/ProjectService.js') }}"></script>```

as also axios, for async connections:

```<script src="{{ asset('js/axios.1.1.2.min.js') }}"></script>```

At least, a controller in symfony or other endpoint in the web has to be defined to handle the ajax calls.
It should return a status code 2xx (200 for example) and render json array like this:

```
[
    {
        id: "123456",
        name: "myname"
    }
]
```
