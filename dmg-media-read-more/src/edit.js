import { __ } from '@wordpress/i18n';
import { Fragment, useState, useEffect } from '@wordpress/element';
import { PanelBody, ComboboxControl } from '@wordpress/components';
import { InspectorControls, useBlockProps } from '@wordpress/block-editor';
import { useEntityRecords } from '@wordpress/core-data';
import { useDebounce } from '@wordpress/compose';

function PostsList({ attributes, setAttributes }) {
    const [posts, setPosts] = useState([]);
    const [selectedPostDetails, setSelectedPostDetails] = useState(null);
    const [selectedPost, setSelectedPost] = useState(attributes.selectedPost || '');
    const [searchQuery, setSearchQuery] = useState('');

    const debouncedSetSearchQuery = useDebounce(setSearchQuery, 200);

    const { records: searchResults, hasResolved: searchResolved } = useEntityRecords('postType', 'post', {
        per_page: 4,
        search_title: searchQuery,
        status: 'publish',
    });

    const { records: selectedPostRecords, hasResolved: selectedPostResolved } = useEntityRecords('postType', 'post', {
        include: selectedPost ? [selectedPost] : [],
        status: 'publish',
    });

    useEffect(() => {
        if (searchResolved) {
            setPosts(searchResults || []);
        }
    }, [searchResults, searchResolved]);

    useEffect(() => {
        if (selectedPostResolved && selectedPostRecords && selectedPostRecords.length > 0) {
            setSelectedPostDetails(selectedPostRecords[0]);
        }
    }, [selectedPostRecords, selectedPostResolved]);

    const onChangePost = (postId) => {
        setSelectedPost(postId);
        setAttributes({ selectedPost: postId });
    };

    return (
        <Fragment>
            <InspectorControls>
                <PanelBody title={__('Select Post')}>
                    <ComboboxControl
                        label={__('Search for a Post')}
                        value={selectedPost}
                        options={posts.map(post => ({ label: post.title.raw, value: post.id }))}
                        onChange={onChangePost}
                        onFilterValueChange={debouncedSetSearchQuery}
                    />
                </PanelBody>
            </InspectorControls>
            <p className="dmg-read-more">
                {selectedPost ? (
                    selectedPostDetails ?
                        <a href={selectedPostDetails.link}>Read More: {selectedPostDetails.title.raw}</a>
                        : "Read More: Loading..."
                ) : "Read More: (Search for and select a post)"}
            </p>
        </Fragment>
    );
}

export default function Edit({ attributes, setAttributes }) {
    return (
        <div {...useBlockProps()}>
            <PostsList attributes={attributes} setAttributes={setAttributes} />
        </div>
    );
}
