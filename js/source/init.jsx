import EUM from './components/main.jsx';
import React from 'react';
import {render} from 'react-dom';



render(
	<EUM options={mpsum.json_options} />, 
	document.getElementById('eum-dashboard-app')
);