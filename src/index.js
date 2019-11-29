import { registerBlockType } from '@wordpress/blocks';
import { TextControl, SelectControl, ToggleControl } from '@wordpress/components';
import { __ } from '@wordpress/i18n';

registerBlockType(
	'awfn/metar',
	{
		title: __( 'AWFN Block' ),
		icon: 'cloud',
		category: 'widgets',
		attributes: {
			apts: {
				type: 'string',
				source: 'attribute',
				selector: 'section',
				attribute: 'data-apts',
				default: ''
			},
			title: {
				type: 'string',
				source: 'attributes',
				selector: 'section',
				attribute: 'data-title',
				default: ''
			},
			radial_dist: {
				type: 'number',
				source: 'attribute',
				selector: 'section',
				attribute: 'data-radial_dist',
				default: 100
			},
			hours: {
				type: 'string',
				source: 'attribute',
				selector: 'section',
				attribute: 'data-hours',
				default: '2'
			},
			show_metar: {
				type: 'string',
				source: 'attribute',
				selector: 'section',
				attribute: 'data-show_metar',
				default: '1'
			},
			show_taf: {
				type: 'string',
				source: 'attribute',
				selector: 'section',
				attribute: 'data-show_taf',
				default: '1'
			},
			show_pireps: {
				type: 'string',
				source: 'attribute',
				selector: 'section',
				attribute: 'data-show_pireps',
				default: '1'
			},
			show_station_info: {
				type: 'string',
				source: 'attribute',
				selector: 'section',
				attribute: 'data-show_station_info',
				default: '1'
			}
		},
		edit: props => {
			const { attributes: { apts, title, radial_dist, hours, show_metar, show_taf, show_pireps, show_station_info }, setAttributes } = props;

			let metarVal   = show_metar !== '0',
			    tafVal     = show_taf !== '0',
			    pirepVal   = show_pireps !== '0',
			    stationVal = show_station_info !== '0';

			const onChangeIcao            = newIcao => {
				setAttributes( { apts: newIcao } );
			};
			const onChangeTitle           = newTitle => {
				setAttributes( { title: newTitle } );
			};
			const onChangeDist            = newDist => {
				setAttributes( { radial_dist: newDist } );
			};
			const onChangeHours           = newHours => {
				setAttributes( { hours: newHours } );
			};
			const onChangeShowMetar       = newMetar => {
				const metar = newMetar ? '1' : '0';
				setAttributes( { show_metar: metar } );
			};
			const onChangeShowTaf         = newTaf => {
				const taf = newTaf ? '1' : '0';
				setAttributes( { show_taf: taf } );
			};
			const onChangeShowPireps      = newPireps => {
				const pireps = newPireps ? '1' : '0';
				setAttributes( { show_pireps: pireps } );
			};
			const onChangeShowStationInfo = newStationInfo => {
				const station = newStationInfo ? '1' : '0';
				setAttributes( { show_station_info: station } );
			};
			return (
				<div>
					<TextControl
						label={ __( 'ICAO' ) }
						onChange={ onChangeIcao }
						value={ apts }
					/>
					<TextControl
						label={ __( 'Custom Title' ) }
						onChange={ onChangeTitle }
						value={ title }
					/>
					<SelectControl
						label={ __( 'Radial Distance' ) }
						onChange={ onChangeDist }
						value={ radial_dist }
						options={ [
							{ value: 25, label: '25' },
							{ value: 50, label: '50' },
							{ value: 100, label: '100' },
							{ value: 125, label: '125' },
							{ value: 150, label: '150' },
							{ value: 175, label: '175' },
							{ value: 200, label: '200' }
						] }
					/>
					<SelectControl
						label={ __( 'Hours Before Now' ) }
						onChange={ onChangeHours }
						value={ hours }
						options={ [
							{ value: 1, label: '1' },
							{ value: 2, label: '2' },
							{ value: 3, label: '3' },
							{ value: 4, label: '4' },
							{ value: 5, label: '5' },
							{ value: 6, label: '6' }
						] }
					/>
					<ToggleControl
						label={ __( 'Show METAR' ) }
						onChange={ onChangeShowMetar }
						checked={ metarVal }
					/>
					<ToggleControl
						label={ __( 'Show TAF' ) }
						onChange={ onChangeShowTaf }
						checked={ tafVal }
					/>
					<ToggleControl
						label={ __( 'Show PIREPS' ) }
						onChange={ onChangeShowPireps }
						checked={ pirepVal }
					/>
					<ToggleControl
						label={ __( 'Show Station Info' ) }
						onChange={ onChangeShowStationInfo }
						checked={ stationVal }
					/>
				</div>
			);
		},
		save: props => {
			console.log( 'props', props );
			const spinnerUrl = opts.spinnerUrl;
			console.log( 'spinner', spinnerUrl );
			const { attributes: { apts, title, radial_dist, hours, show_metar, show_taf, show_pireps, show_station_info } } = props;
			const { attributes }                                                                                            = props;
			console.log( 'atts', attributes );
			return (
				<section
					className="awfn-shortcode"
					data-apts={ apts }
					data-title={ title }
					data-radial_dist={ radial_dist }
					data-hours={ hours }
					data-show_metar={ show_metar }
					data-show_taf={ show_taf }
					data-show_pireps={ show_pireps }
					data-show_station_info={ show_station_info }
					data-atts={ JSON.stringify( attributes ) }
				>
					<img className="sc-loading" src={ spinnerUrl }/>
				</section>
			);
		}
	}
);
