/**
 * The javascript fetches all albums and their artwork and displays thme in a Grid
 *
 * The albums all link to a page with their specific potos
 *
 * The code using
 */
( function( fbas ,$ , _ , Backbone , React ){

    var allPhotos = new fbas.PhotoCollection();
    var albumId = '';

    if( "undefined" == typeof fbasAlbumId ){
        albumId = albuallPhotos.albumId
    }else{
        albumId = fbasAlbumId
    }

    var albumPhotosUrl = "https://graph.facebook.com/"+ albumId +"/photos";
    allPhotos.fetchPhotos( albumPhotosUrl );


    /**
     * Merge React and Backbone
     */
    var allPhotosCollectionMixin = {
        componentDidMount: function() {
            // Whenever there may be a change in the Backbone data, trigger a reconcile.
            this.getBackboneCollection().on('update add change remove',this.update.bind(this, null), this);
        },

        componentWillUnmount: function() {
            // Ensure that we clean up any dangling references when the component is
            // destroyed.
            this.getBackboneCollection().off(null, null, this);
        }
    };

    var PhotoComponent = React.createClass({displayName: "PhotoComponent",
        /**
         * set the initial state
         * to be the model
         * @returns {*}
         */
        getInitialState: function(){
            return this.props.model;
        },

        render: function(){
            var imageClass = 'fbas-image photothumblarge';
            var imageStyle = { backgroundSize: 'cover', backgroundImage: 'url("'+ this.state.standardImage.source +'")' };
            return(
                React.createElement("a", {className: "photolink", href: this.state.largestImage.source, "data-lightbox": "fbgallery"}, 
                    React.createElement("img", {src: "", style: imageStyle, className: imageClass})
                )
            );
        }
    });

    /**
     * all Photos Component. Serves ass the holder
     * for all the Album components.
     */
    var AllPhotosComponent = React.createClass({displayName: "AllPhotosComponent",
        /**
         * linking Backbone to react
         */
        mixins: [allPhotosCollectionMixin],

        getBackboneCollection: function(){
            return this.state;
        },

        getInitialState: function(){
            return this.props.photos;
        },
        update: function( e,change ){
            this.forceUpdate();
        },

        /**
         * Render the component
         */
        render: function() {

            var loaderClass = 'hidden';
            if( this.state.isLoading ) {
                loaderClass = 'visible';
            }

            return (
                React.createElement("ul", null, 
                [ React.createElement("div", {className: loaderClass, id: "fbloader"}) ,
                    this.state.models.map( function(photo, index ) {
                        var noOfColumns = 4;
                        var res = ( index + 1 ) % noOfColumns;
                        var last = 0==res ? 'last': '';
                        var liClass= "col-1-" + noOfColumns + " " + last;
                        return( React.createElement("li", {key: photo.attributes.id, id: photo.attributes.id, className: liClass}, 
                            React.createElement(PhotoComponent, {model: photo})

                        ))

                    })// end state map function
                ]
                )
            );
        }
    });


    /**
     * Main React Render Call for the singel album page
     */
    React.render(
        React.createElement(AllPhotosComponent, {photos: allPhotos}),
        document.getElementById('fbalbumsync')
    );


} ( window.fbas, jQuery , _ , Backbone, React ) ) ;