import React, {PureComponent} from 'react';
import {PropTypes} from 'prop-types';
import {Link} from "react-router-dom";

class PublicationNavigation extends PureComponent {
    static propTypes = {
        parents: PropTypes.array.isRequired,
        currentTitle: PropTypes.string.isRequired,
        children: PropTypes.array.isRequired,
    };

    render() {
        const {parents, children, currentTitle} = this.props;

        return <>
            <ul className="list-unstyled components mb-5">
                {parents.map(p => <li
                    key={p.id}
                >
                    <Link to={`/${p.id}`}>
                        {p.title}
                    </Link>
                </li>)}
            </ul>
            <h2>{currentTitle}</h2>
            <NavTree children={children}/>
        </>
    }
}

class NavTree extends PureComponent {
    static propTypes = {
        children: PropTypes.array.isRequired,
    };

    render() {
        return <ul className="list-unstyled components">
            {this.props.children.map(c => <li
                key={c.id}
            >
                <Link to={`/${c.id}`}>
                    {c.title}
                </Link>
                {c.children && c.children.length > 0 ?
                    <NavTree children={c.children}/>
                    : ''}
            </li>)}
        </ul>
    }
}

export default PublicationNavigation;