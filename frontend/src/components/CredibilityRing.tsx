interface Props {
  score: number;
  band: 'excellent' | 'good' | 'fair' | 'poor';
  size?: number;
}

const BAND_COLORS = {
  excellent: '#10B981',
  good:      '#3B82F6',
  fair:      '#F59E0B',
  poor:      '#EF4444',
};

const BAND_LABELS = {
  excellent: 'Excellent',
  good:      'Good',
  fair:      'Fair',
  poor:      'Poor',
};

export default function CredibilityRing({ score, band, size = 180 }: Props) {
  const r = (size / 2) - 16;
  const circumference = 2 * Math.PI * r;
  const fill = (score / 100) * circumference;
  const color = BAND_COLORS[band];

  return (
    <div className="flex flex-col items-center gap-2">
      <svg width={size} height={size} style={{ transform: 'rotate(-90deg)' }}>
        <circle
          cx={size / 2} cy={size / 2} r={r}
          fill="none" stroke="#E5E7EB" strokeWidth="14"
        />
        <circle
          cx={size / 2} cy={size / 2} r={r}
          fill="none" stroke={color} strokeWidth="14"
          strokeDasharray={`${fill} ${circumference}`}
          strokeLinecap="round"
          style={{ transition: 'stroke-dasharray 0.8s ease' }}
        />
        <text
          x="50%" y="50%"
          textAnchor="middle" dominantBaseline="middle"
          style={{ transform: 'rotate(90deg)', transformOrigin: 'center', fontSize: size * 0.22, fontWeight: 700, fill: color }}
        >
          {score.toFixed(1)}%
        </text>
        <text
          x="50%" y="65%"
          textAnchor="middle" dominantBaseline="middle"
          style={{ transform: 'rotate(90deg)', transformOrigin: 'center', fontSize: size * 0.09, fill: '#6B7280' }}
        >
          {BAND_LABELS[band]}
        </text>
      </svg>
      <span className="text-sm font-medium text-gray-600">Overall Credibility Score</span>
    </div>
  );
}
