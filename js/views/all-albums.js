/**
 * The javascript fetches all albums and their artwork and displays thme in a Grid
 *
 * The albums all link to a page with their specific potos
 *
 * The code using
 */
( function( fbas ,$ , _ , Backbone , React ){

    //facbookAlbumsSync is localized through WordPress
    var ALbumsCount = 1;
    var rowItemscnt = 1;
    var excludeAlbums = facbookAlbumsSync.excludeAlbums;
    var prettypermalinkon = facbookAlbumsSync.prettyPermalinks ;

    // exclude albums
    for (albumid in excludeAlbums){
        excludeAlbums[albumid] = excludeAlbums[albumid].trim();
    }

    var apiUrl = "https://graph.facebook.com/" +  facbookAlbumsSync.facebookPageName + "/albums/";

    // setup new collection to hold all albums
    allAlbums = new fbas.AlbumCollection();
    allAlbums.url = apiUrl;

    // fetch the albums and fill the albums collection
    allAlbums.sync();

    /**
     * Merge React and Backbone
     */
    var AlbumCollectionMixin = {
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

    var AlbumModelMixin = {
        componentDidMount: function() {
            // Whenever there may be a change in the Backbone data, trigger a reconcile.
            this.getModel().on('add change remove',this.update.bind(this, null), this);
        },

        componentWillUnmount: function() {
            // Ensure that we clean up any dangling references when the component is
            // destroyed.
            this.getModel().off(null, null, this);
        }
    };

    // Individual album component
    var AlbumComponent = React.createClass({displayName: "AlbumComponent",

        mixins: [AlbumModelMixin],

        getInitialState:function(){
            this.props.albumModel.set({photoUrl: 'https://www.facebookbrand.com/img/assets/asset.f.logo.lg.png'})
            return this.props.albumModel;
        },

        update: function( e, changedModel ){
            this.forceUpdate();
        },

        /**
         * Return state which is the backbone model
         */
        getModel: function(){
            return this.state;
        },
        /**
         * @returns XML
         */
        render: function(){
            var albumStyle = { backgroundImage: 'url("'+this.state.get('photoUrl')+'")' };
            var linkToAlbumPage = document.URL+"?fbasid=" + this.state.id;
           return(  React.createElement("a", {className: "albumlink", href: linkToAlbumPage}, 
                React.createElement("img", {key: this.state.id, style: albumStyle}), 
                React.createElement("div", {className: "album-name"}, " ",  this.state.attributes.name, " ")
           ));
        } // end render
    });

    /**
     * AblumS Component. Serves ass the holder
     * for all the Album components.
     */
    var AlbumsComponent = React.createClass({displayName: "AlbumsComponent",
        mixins: [AlbumCollectionMixin],
        getBackboneCollection: function() {
            return allAlbums;
        },
        getInitialState: function(){
            return {models: [ ] };
        },
        update: function( e,change ){

            //todo: why is change empty at times?
            if( ! _.isEmpty( change ) ) {
                this.setState(change.collection);
            }

            this.forceUpdate();

        },

        /**
        * Render the component
        */
        render: function() {

            var loaderClass = 'visible';
            if( _.isEmpty( this.state.loadingQueue )){
                var loaderClass = 'hidden';
            }


            return (
                React.createElement("ul", null, 
                [ React.createElement("div", {className: loaderClass, id: "fbloader"}) ,
                    this.state.models.map( function(album, index ) {
                        var noOfColumns = 4;
                        var res = ( index + 1 ) % noOfColumns;
                        var last = 0==res ? 'last': '';
                        var liClass= "col-1-4 " + last;
                        return( React.createElement("li", {key: album.attributes.id, id: album.attributes.id, className: liClass}, 
                                    React.createElement(AlbumComponent, {albumModel: album})
                                ))

                    })// end state map function
                ]
                )
            );
        }
    });

    /**
     * Main Render Call
     */
    React.render(
    React.createElement(AlbumsComponent, {albums: allAlbums}),
        document.getElementById('fbalbumsync')
    );

} ( window.fbas, jQuery , _ , Backbone, React ) ) ;