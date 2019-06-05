import React, {Component} from 'react';
import '../../scss/Upload.scss';
import PropTypes from "prop-types";
import AssetForm from "../AssetForm";
import Container from "../Container";

export default class UploadForm extends Component {
    static propTypes = {
        files: PropTypes.array.isRequired,
        onNext: PropTypes.func.isRequired,
    };

    onComplete = (data) => {
        console.debug('data', data);
        this.props.onNext(data);
    };

    render() {
        const {files} = this.props;

        return <Container>
            <p>
                {files.length} selected files.
            </p>

            <AssetForm
                onComplete={this.onComplete}
            />
        </Container>;
    }
}
