import React from 'react';
import {render} from 'react-dom';
import LoadingGif from './loading.jsx';
import EUM from './main.jsx';
import EUMActions from '../data/EUMActions.jsx';

class ToggleItemRadio extends React.Component {
	
	constructor(props) {
		super(props);
		this.state = {
			checked     : this.props.checked,
			disabled    : this.props.disabled,
			label       : mpsum.enabled,
			loading     : false,
			choices     : this.props.choices
		};
		this.itemChange = this.itemChange.bind(this);
	}
	itemChange(e) {
		e.preventDefault();
		this.setState({loading:true});
		EUMActions.itemToggle( this.props.context, this.props.name, e.target.value );
		
	}
	displayChoices() {
		
		if ( false === this.state.loading ) {
			var choices = [];
			for( var choice of this.props.choices ) {
				choices.push(
					<div key={choice.id} className="multi-choice-item">
						<input type="radio" value={choice.value} id={choice.id} checked={this.state.checked==choice.value ? true : false } onChange={this.itemChange} disabled={this.state.disabled} />
						&nbsp;
						<label htmlFor={choice.id}>{choice.label}</label>
					</div>	
				);
			}
			return (
				<div className="multi-choice">
					{choices}
				</div>
			)
		} else {
			return(
				<LoadingGif />
			)
		}
		
	}
	componentWillReceiveProps(newprops) {
		this.setState({
			loading:newprops.loading,
			checked:newprops.checked,
			disabled:newprops.disabled
		});
	}
	render() {
		return (
			<div>
				<div className="dashboard-item active">
					<div className="dashboard-item-header input-radio">
						{this.props.title}	
					</div>		
					{this.displayChoices()}
				</div>	
			</div>	
		);
	}
}
ToggleItemRadio.propTypes = {
	checked: React.PropTypes.string.isRequired,
	title: React.PropTypes.string.isRequired,
	disabled: React.PropTypes.bool.isRequired,
	name: React.PropTypes.string.isRequired,
	context: React.PropTypes.string.isRequired,
	loading: React.PropTypes.bool.isRequired
};
ToggleItemRadio.defaultProps = {
	checked: 'default',
	title: '',
	disabled: false,
	name: '',
	context: '',
	loading: false
};
export default ToggleItemRadio;