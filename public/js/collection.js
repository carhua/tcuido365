/**
 Admin script
 **/
var Collection = function () {
    //* BEGIN:CORE HANDLERS *//

    var createChild = function (child,child_class) {
        // creamos el contenedor
        child = typeof child !== 'undefined' ? child : 'li';
        var custom_child = '<' + child + ' class="' + child_class + ' list-group-item p-1"></' + child + '>' ;

        return custom_child;
    };

    // Handle Entity
    var handleEntity = function (collection, child) {
        var $addLink = $('<a href="#" class="btn btn-outline-success btn-sm tm-btn m-btn--icon m-btn--pill"><span><i class="fa flaticon-plus"></i><span>Agregar</span></span></a>');
        var $newLink = $(child).append($addLink);
        initDataForm(collection, $addLink, $newLink, child);
    };

    var initDataForm = function(collection, $addLink, $newLink, child){
        var $collectionHolder = $(collection);
        $collectionHolder.children('li').each(function() { // reemplazo find
            addBlockFormDeleteLink($(this));
        });
        $collectionHolder.append($newLink);
        //$collectionHolder.data('index', $collectionHolder.find(':input').length);
        $collectionHolder.data('index', $collectionHolder.find('li').length);
        $addLink.on('click', function(e) {
            e.preventDefault();
            addBlockForm($collectionHolder, $newLink, child);
        });
    };

    var addBlockForm = function($collectionHolder, $newLink, child) {
        var prototype = $collectionHolder.data('prototype');
        var index = $collectionHolder.data('index');
        var newForm;
        if($collectionHolder.data('prototype-name')){
            var prototype_name = new RegExp($collectionHolder.data('prototype-name'), 'gi');
            newForm = prototype.replace(prototype_name, index);
        }
        else{
            newForm = prototype.replace(/__name__/g, index);
        }
        $collectionHolder.data('index', index + 1);
        var $newFormLi = $(child).append(newForm);
        $newLink.before($newFormLi); //$newLink.append($newFormLi);
        addBlockFormDeleteLink($newFormLi);

        return $newFormLi;
    };

    var addBlockFormDeleteLink = function($formLi) {
        var $removeFormA = $('<a href="#" class="btn btn-hover-danger btn-sm btn-icon btn-circle" style="position: absolute; top: 0; right: 0;"><i class="la la-remove"></i></a>');
        //var $auxForm = $('<div class="col-sm-1"></div>').append($removeFormA);
        //$tagFormLi.children('.row').append($auxForm);
        $formLi.append($removeFormA);
        $removeFormA.on('click', function(e) {
            // prevent the link from creating a "#" on the URL
            e.preventDefault();
            if(confirm('Esta seguro de eliminar')){
                // remove the li for the tag form
                $formLi.remove();
            }
        });
    };

    var initDataForm2 = function(collection, $addLink, $newLink, child){
        var $collectionHolder = $(collection);
        $collectionHolder.children('li').each(function() { // reemplazo find
            addBlockFormDeleteLink($(this));
        });
        $collectionHolder.append($newLink);
        //$collectionHolder.data('index', $collectionHolder.find(':input').length);
        $collectionHolder.data('index', $collectionHolder.find('li').length);
        $addLink.on('click', function(e) {
            e.preventDefault();
            addBlockForm($collectionHolder, $newLink, child);
        });
    };

    let addBlockFormGuiaAcopio = function(collection) {
        let $collectionHolder = $(collection);
        let prototype = $collectionHolder.data('prototype');
        let index = $collectionHolder.data('index');
        let newForm;
        if($collectionHolder.data('prototype-name')){
            let prototype_name = new RegExp($collectionHolder.data('prototype-name'), 'gi');
            newForm = prototype.replace(prototype_name, index);
        }
        else{
            newForm = prototype.replace(/__name__/g, index);
        }

        $collectionHolder.data('index', index + 1);
        let child = "<li class='collection-item' style='position: relative;'></li>";
        let $newFormLi = $(child).append(newForm);

        $collectionHolder.append($newFormLi);
        addBlockFormDeleteLink($newFormLi);

        return $newFormLi;
    };

    //* END:CORE HANDLERS *//

    return {
        init: function (collection,child) {
            var custom_child = createChild(child,'collection_child');
            handleEntity(collection,custom_child);
        },
        show: function (collection, addbutton=true) {
            let hidden = '';
            if(addbutton === false){
                hidden = 'kt-hidden';
            }

            let child = "<li class='collection-item'></li>";
            var $addLink = $('<a href="#" class="' + hidden + ' btn btn-outline-success btn-sm tm-btn"><span><i class="fa flaticon-plus"></i><span>Agregar</span></span></a>');
            var $newLink = $(child).append($addLink);

            initDataForm2(collection, $addLink, $newLink, child);
        },
        addFormGuia: function (collection) {
            return addBlockFormGuiaAcopio(collection);
        }
    };

}();
